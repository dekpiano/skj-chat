<?php

namespace Config;

use CodeIgniter\Config\Routing as BaseRouting;

/**
 * Routing Configuration
 */
class Routing extends BaseRouting
{
    /**
     * @var string
     */
    public string $defaultNamespace = 'App\Controllers';

    /**
     * @var string
     */
    public string $defaultController = 'Home';

    /**
     * @var string
     */
    public string $defaultMethod = 'index';

    /**
     * @var bool
     */
    public bool $translateURIDashes = false;

    /**
     * @var string|null
     */
    public ?string $override404 = null;

    /**
     * @var bool
     */
    public bool $autoRoute = false;

    /**
     * @var bool
     */
    public bool $prioritize = false;


}
