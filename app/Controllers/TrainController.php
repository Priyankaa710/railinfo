<?php

namespace App\Controllers;

use App\Libraries\RailApiClient;
use App\Models\ScheduleModel;
use App\Models\StationModel;
use App\Models\TrainModel;
use App\Models\TravelHistoryModel;
use Config\RailApi;

class TrainController extends BaseController
{
    protected StationModel $stationModel;
    protected ScheduleModel $scheduleModel;
    protected TrainModel $trainModel;
    protected TravelHistoryModel $historyModel;
    protected RailApi $railApiConfig;

    public function __construct()
    {
        $this->stationModel  = new StationModel();
        $this->scheduleModel = new ScheduleModel();
        $this->trainModel    = new TrainModel();
        $this->historyModel  = new TravelHistoryModel();
        $this->railApiConfig = config('RailApi');
    }

    /**
     * GET /trains — the search form.
     */
    public function index(): string
    {
        $data = [
            'title'   => 'Train Schedule Checker — RailInfo',
            'sources' => $this->stationModel->getPopularRoutes(),
        ];

        return view('templates/header', $data)
            . view('trains/search', $data)
            . view('templates/footer', $data);
    }

    /**
     * POST /trains/search — validates the form and redirects to the
     * results page with query params (keeps results bookmarkable).
     */
    public function search()
    {
        $rules = [
            'source'      => 'required|max_length[10]',
            'destination' => 'required|max_length[10]|differs[source]',
            'travel_date' => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $source      = strtoupper($this->request->getPost('source'));
        $destination = strtoupper($this->request->getPost('destination'));
        $date        = $this->request->getPost('travel_date');

        // Save to session travel history (most recent first, capped at 8).
        $history = $this->session->get('travel_history') ?? [];
        array_unshift($history, [
            'source'      => $source,
            'destination' => $destination,
            'date'        => $date,
            'at'          => date('d M Y, h:i A'),
        ]);
        $this->session->set('travel_history', array_slice($history, 0, 8));

        // Durable copy in MySQL as well.
        $this->historyModel->logSearch(session_id(), [
            'search_type'      => 'schedule',
            'source_code'      => $source,
            'destination_code' => $destination,
            'pnr_number'       => null,
            'travel_date'      => $date,
        ]);

        return redirect()->to(
            url_to('TrainController::results') . '?' . http_build_query([
                'source'      => $source,
                'destination' => $destination,
                'travel_date' => $date,
            ])
        );
    }

    /**
     * GET /trains/results — reads from MySQL cache first; if the cache
     * is stale or empty it calls the external API via the HTTP client
     * and stores the response back into MySQL for next time.
     */
    public function results(): string
    {
        $source      = strtoupper($this->request->getGet('source') ?? '');
        $destination = strtoupper($this->request->getGet('destination') ?? '');
        $date        = $this->request->getGet('travel_date') ?? '';

        $data = [
            'title'       => "Trains from {$source} to {$destination} — RailInfo",
            'source'      => $source,
            'destination' => $destination,
            'travel_date' => $date,
            'fromCache'   => false,
            'results'     => [],
        ];

        if ($source === '' || $destination === '' || $date === '') {
            $data['results'] = [];

            return view('templates/header', $data)
                . view('trains/results', $data)
                . view('templates/footer', $data);
        }

        $isFresh = $this->scheduleModel->isFresh($source, $destination, $date, $this->railApiConfig->cacheTtl);

        if (! $isFresh) {
            $api  = new RailApiClient();
            $live = $api->fetchSchedule($source, $destination, $date);

            if ($live && ! empty($live['schedules'])) {
                $rows = [];
                foreach ($live['schedules'] as $item) {
                    $train = $this->trainModel->findByNumber($item['train_number']) ?? [
                        'id' => null,
                    ];

                    if (empty($train['id'])) {
                        $trainId = $this->trainModel->insert([
                            'train_number'      => $item['train_number'],
                            'train_name'        => $item['train_name'],
                            'train_type'        => $item['train_type'] ?? 'EXP',
                            'source_code'       => $source,
                            'destination_code'  => $destination,
                            'departure_time'    => $item['departure_time'],
                            'arrival_time'      => $item['arrival_time'],
                            'duration'          => $item['duration'] ?? null,
                            'distance_km'       => $item['distance_km'] ?? null,
                        ], true);
                    } else {
                        $trainId = $train['id'];
                    }

                    $rows[] = [
                        'train_id'         => $trainId,
                        'source_code'      => $source,
                        'destination_code' => $destination,
                        'travel_date'      => $date,
                        'departure_time'   => $item['departure_time'],
                        'arrival_time'     => $item['arrival_time'],
                        'duration'         => $item['duration'] ?? null,
                        'sl_seats'         => $item['seats']['SL'] ?? null,
                        'sl_fare'          => $item['fare']['SL'] ?? null,
                        'ac3_seats'        => $item['seats']['3A'] ?? null,
                        'ac3_fare'         => $item['fare']['3A'] ?? null,
                        'ac2_seats'        => $item['seats']['2A'] ?? null,
                        'ac2_fare'         => $item['fare']['2A'] ?? null,
                        'ac1_seats'        => $item['seats']['1A'] ?? null,
                        'ac1_fare'         => $item['fare']['1A'] ?? null,
                        'status'           => $item['status'] ?? 'ON_TIME',
                    ];
                }

                $this->scheduleModel->storeFromApi($rows);
            }
        }

        $data['results']   = $this->scheduleModel->findCached($source, $destination, $date);
        $data['fromCache'] = true;

        return view('templates/header', $data)
            . view('trains/results', $data)
            . view('templates/footer', $data);
    }

    /**
     * GET /trains/{number} — single train detail (running days, route).
     */
    public function show(string $number): string
    {
        $train = $this->trainModel->findByNumber($number);

        $data = [
            'title' => $train ? "{$train['train_name']} ({$train['train_number']})" : 'Train Not Found',
            'train' => $train,
            'days'  => $train ? $this->trainModel->runningDaysArray($train) : [],
        ];

        return view('templates/header', $data)
            . view('trains/show', $data)
            . view('templates/footer', $data);
    }

    /**
     * GET /trains/station-suggest?term=... — AJAX autocomplete endpoint
     * backed entirely by the CI4 Query Builder (StationModel::search).
     */
    public function stationSuggest()
    {
        $term = $this->request->getGet('term') ?? '';

        return $this->response->setJSON(
            $this->stationModel->search($term)
        );
    }

    public function clearHistory()
    {
        $this->session->remove('travel_history');
        $this->historyModel->clearForSession(session_id());

        return redirect()->to(url_to('Home::index'))->with('message', 'Travel history cleared.');
    }
}
