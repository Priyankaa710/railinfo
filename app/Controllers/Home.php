<?php

namespace App\Controllers;

use App\Models\StationModel;

class Home extends BaseController
{
    public function index(): string
    {
        $stationModel = new StationModel();

        $data = [
            'title'        => 'RailInfo — Train Schedule & PNR Status Portal',
            'popularRoutes' => $stationModel->getPopularRoutes(),
            'recentSearches' => $this->session->get('travel_history') ?? [],
            'stats' => [
                'trains'   => 13000,
                'stations' => 7300,
                'daily'    => '2.3 crore+',
            ],
        ];

        return view('templates/header', $data)
            . view('home', $data)
            . view('templates/footer', $data);
    }

    public function about(): string
    {
        $data = ['title' => 'About RailInfo'];

        return view('templates/header', $data)
            . view('about', $data)
            . view('templates/footer', $data);
    }

    /**
     * Custom 404 handler, registered via $routes->set404Override().
     */
    public function notFound(): string
    {
        $this->response->setStatusCode(404);
        $data = ['title' => 'Page Not Found'];

        return view('templates/header', $data)
            . view('errors/custom404', $data)
            . view('templates/footer', $data);
    }
}
