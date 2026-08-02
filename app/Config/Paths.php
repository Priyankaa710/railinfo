<?php

namespace Config;

/**
 * The Paths config file.
 *
 * This file is a bridge between the front controller (public/index.php)
 * and the rest of the framework. Because the "system" directory ships
 * via Composer (codeigniter4/framework), it lives at project-root
 * /vendor/codeigniter4/framework/system after running `composer install`.
 */
class Paths
{
    /**
     * The path to the project root directory. Just above APPPATH.
     */
    public string $appDirectory = __DIR__ . '/../';

    /**
     * The path to the system directory. Populated by Composer.
     */
    public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';

    /**
     * The path to the writable directory.
     */
    public string $writableDirectory = __DIR__ . '/../../writable';

    /**
     * The path to the tests directory.
     */
    public string $testsDirectory = __DIR__ . '/../../tests';

    /**
     * The path to the view directory.
     */
    public string $viewDirectory = __DIR__ . '/../Views';
}
