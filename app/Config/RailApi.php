<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Settings for the external railway data provider consumed via
 * CI4's HTTP Client (see app/Libraries/RailApiClient.php).
 *
 * Populate railapi.baseURL and railapi.key in your .env file.
 */
class RailApi extends BaseConfig
{
    /**
     * Base URL of the external railway API.
     */
    public string $baseURL = 'https://api.example-rail-provider.com/';

    /**
     * API key / token for the external provider.
     */
    public string $apiKey = '';

    /**
     * Timeout (seconds) for outgoing HTTP requests.
     */
    public int $timeout = 8;

    /**
     * How long (seconds) cached schedule/PNR data stays "fresh"
     * before we attempt to refresh it from the live API again.
     */
    public int $cacheTtl = 900; // 15 minutes
}
