<?php

/*
 * --------------------------------------------------------------------
 * RailInfo — Front Controller
 * --------------------------------------------------------------------
 * This is the standard CodeIgniter 4 front controller. It bootstraps
 * the framework and dispatches every request. It assumes the CI4
 * framework itself (the "system" directory + Composer autoloader) has
 * been installed via `composer install` at the project root — see
 * README.md for the full deployment steps for Hostinger.
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing here
chdir(FCPATH);

/*
 * --------------------------------------------------------------------
 * Load our paths config file
 * --------------------------------------------------------------------
 * This is the line that might need to be changed, depending on the
 * folder structure of your project.
 */
require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();

/*
 * --------------------------------------------------------------------
 * Bootstrap the application
 * --------------------------------------------------------------------
 */
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

exit($app->run());
