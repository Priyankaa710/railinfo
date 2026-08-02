<?php

namespace App\Controllers;

use App\Libraries\RailApiClient;
use App\Models\PnrCacheModel;
use App\Models\TravelHistoryModel;
use Config\RailApi;

class PNRController extends BaseController
{
    protected PnrCacheModel $pnrModel;
    protected TravelHistoryModel $historyModel;
    protected RailApi $railApiConfig;

    public function __construct()
    {
        $this->pnrModel      = new PnrCacheModel();
        $this->historyModel  = new TravelHistoryModel();
        $this->railApiConfig = config('RailApi');
    }

    /**
     * GET /pnr — the PNR entry form.
     */
    public function index(): string
    {
        $data = ['title' => 'PNR Status Tracker — RailInfo'];

        return view('templates/header', $data)
            . view('pnr/search', $data)
            . view('templates/footer', $data);
    }

    /**
     * POST /pnr/track — validate the PNR number and redirect to result.
     */
    public function track()
    {
        $rules = [
            'pnr_number' => 'required|exact_length[10]|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pnr = $this->request->getPost('pnr_number');

        $this->historyModel->logSearch(session_id(), [
            'search_type'      => 'pnr',
            'source_code'      => null,
            'destination_code' => null,
            'pnr_number'       => $pnr,
            'travel_date'      => null,
        ]);

        return redirect()->to(url_to('PNRController::result', $pnr));
    }

    /**
     * GET /pnr/result/{pnr} — cache-first PNR lookup, live API fallback.
     */
    public function result(string $pnr): string
    {
        $data = [
            'title'     => "PNR {$pnr} Status — RailInfo",
            'pnr'       => $pnr,
            'record'    => null,
            'notFound'  => false,
        ];

        $cached = $this->pnrModel->findByPnr($pnr);

        if (! $cached || ! $this->pnrModel->isFresh($cached, $this->railApiConfig->cacheTtl)) {
            $api  = new RailApiClient();
            $live = $api->fetchPnrStatus($pnr);

            if ($live) {
                $this->pnrModel->upsert($pnr, [
                    'train_number'     => $live['train_number'] ?? null,
                    'train_name'       => $live['train_name'] ?? null,
                    'journey_date'     => $live['journey_date'] ?? null,
                    'source_code'      => $live['source_code'] ?? null,
                    'destination_code' => $live['destination_code'] ?? null,
                    'boarding_point'   => $live['boarding_point'] ?? null,
                    'class'            => $live['class'] ?? null,
                    'chart_prepared'   => ! empty($live['chart_prepared']) ? 1 : 0,
                    'passenger_count'  => $live['passenger_count'] ?? count($live['passengers'] ?? []),
                    'passengers_json'  => json_encode($live['passengers'] ?? []),
                    'current_status'   => $live['current_status'] ?? 'UNKNOWN',
                ]);

                $cached = $this->pnrModel->findByPnr($pnr);
            }
        }

        if ($cached) {
            $cached['passengers'] = json_decode($cached['passengers_json'] ?? '[]', true) ?: [];
            $data['record']       = $cached;
        } else {
            $data['notFound'] = true;
        }

        return view('templates/header', $data)
            . view('pnr/result', $data)
            . view('templates/footer', $data);
    }

    /**
     * POST /api/pnr-check — lightweight JSON endpoint used by the
     * homepage quick-check widget (progressive enhancement over form POST).
     */
    public function apiCheck()
    {
        $pnr = $this->request->getPost('pnr_number');

        if (! preg_match('/^\d{10}$/', (string) $pnr)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'    => false,
                'error' => 'PNR must be exactly 10 digits.',
            ]);
        }

        return $this->response->setJSON([
            'ok'  => true,
            'url' => url_to('PNRController::result', $pnr),
        ]);
    }
}
