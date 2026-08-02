<?php

namespace App\Libraries;

use Config\RailApi;
use Config\Services;

/**
 * Thin wrapper around CodeIgniter's CURLRequest (HTTP Client) used to
 * talk to the external railway data provider. Every call is wrapped
 * in a try/catch so that a provider outage degrades gracefully to
 * the MySQL cache instead of breaking the page.
 */
class RailApiClient
{
    protected RailApi $config;

    public function __construct()
    {
        $this->config = config('RailApi');
    }

    /**
     * Fetch live schedule data for a source/destination pair.
     *
     * @return array|null Null on failure (caller should fall back to cache).
     */
    public function fetchSchedule(string $source, string $destination, string $date): ?array
    {
        try {
            $client = Services::curlrequest([
                'base_uri' => $this->config->baseURL,
                'timeout'  => $this->config->timeout,
            ]);

            $response = $client->get('v1/schedules', [
                'query' => [
                    'from' => $source,
                    'to'   => $destination,
                    'date' => $date,
                    'key'  => $this->config->apiKey,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $body = json_decode($response->getBody(), true);

            return is_array($body) ? $body : null;
        } catch (\Throwable $e) {
            log_message('error', 'RailApiClient::fetchSchedule failed: {msg}', ['msg' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Fetch live PNR status for a given 10-digit PNR number.
     *
     * @return array|null Null on failure (caller should fall back to cache).
     */
    public function fetchPnrStatus(string $pnr): ?array
    {
        try {
            $client = Services::curlrequest([
                'base_uri' => $this->config->baseURL,
                'timeout'  => $this->config->timeout,
            ]);

            $response = $client->get('v1/pnr/' . $pnr, [
                'query' => ['key' => $this->config->apiKey],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $body = json_decode($response->getBody(), true);

            return is_array($body) ? $body : null;
        } catch (\Throwable $e) {
            log_message('error', 'RailApiClient::fetchPnrStatus failed: {msg}', ['msg' => $e->getMessage()]);

            return null;
        }
    }
}
