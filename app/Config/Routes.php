<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('Home::notFound');
$routes->setAutoRoute(false);

// ---------------------------------------------------------------
// Public site
// ---------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');

// ---------------------------------------------------------------
// Train schedule module
// ---------------------------------------------------------------
$routes->get('trains', 'TrainController::index');
$routes->post('trains/search', 'TrainController::search');
$routes->get('trains/results', 'TrainController::results');
$routes->get('trains/(:num)', 'TrainController::show/$1');
$routes->get('trains/station-suggest', 'TrainController::stationSuggest'); // AJAX autocomplete
$routes->get('trains/history/clear', 'TrainController::clearHistory');

// ---------------------------------------------------------------
// PNR module
// ---------------------------------------------------------------
$routes->get('pnr', 'PNRController::index');
$routes->post('pnr/track', 'PNRController::track');
$routes->get('pnr/result/(:segment)', 'PNRController::result/$1');

// ---------------------------------------------------------------
// API (JSON) helper endpoints consumed by the frontend JS
// ---------------------------------------------------------------
$routes->group('api', static function ($routes) {
    $routes->get('stations', 'TrainController::stationSuggest');
    $routes->post('pnr-check', 'PNRController::apiCheck');
});
