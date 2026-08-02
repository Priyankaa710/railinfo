<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * Overridden by app.baseURL in the .env file for each environment.
     */
    public string $baseURL = 'http://localhost:8080/';

    public array $allowedHostnames = [];

    public string $indexPage = '';

    public string $uriProtocol = 'REQUEST_URI';

    public string $defaultLocale = 'en';

    public bool $negotiateLocale = false;

    public array $supportedLocales = ['en'];

    public string $appTimezone = 'Asia/Kolkata';

    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false;

    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    public bool $CSRFProtection = false; // enabled via filter selectively if desired

    public bool $CSPEnabled = false;
}
