<?php

namespace Config;

use CodeIgniter\Modules\Modules as BaseModules;

class Modules extends BaseModules
{
    /**
     * --------------------------------------------------------------------------
     * Enable Auto-Discovery?
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all elements listed in
     * $aliases below. If false, no auto-discovery will happen at all,
     * giving a slight performance boost.
     *
     * @var bool
     */
    public $discover = true;

    /**
     * --------------------------------------------------------------------------
     * Enable Auto-Discovery Within Composer Packages?
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all namespaces loaded
     * by Composer, as well as the namespaces configured locally.
     *
     * @var bool
     */
    public $discoverInVendor = true;

    /**
     * --------------------------------------------------------------------------
     * Composer Package Auto-Discovery
     * --------------------------------------------------------------------------
     *
     * If $discoverInVendor is true, then auto-discovery will happen across
     * all namespaces loaded by Composer, as well as the namespaces configured
     * locally.
     *
     * @var array{only: string[], exclude: string[]}
     */
    public $composerPackages = [
        'only'    => [],
        'exclude' => [
            // List any packages here that you want to exclude from auto-discovery.
        ],
    ];

    /**
     * --------------------------------------------------------------------------
     * Auto-Discovery Rules
     * --------------------------------------------------------------------------
     *
     * Aliases list of all discovery classes that will be active and used during
     * the current application request.
     *
     * If it is not listed, only the base application elements will be used.
     *
     * @var string[]
     */
    public $aliases = [
        'events',
        'filters',
        'registrars',
        'routes',
        'services',
    ];
}
