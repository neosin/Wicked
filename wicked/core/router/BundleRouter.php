<?php

namespace wicked\core\router;

use wicked\core\Router;

class BundleRouter extends Router
{

    public function __construct(array $config = [])
    {
        // default config
        $config = [
            'base' => null,
            'bundle' => 'front',
            'controller' => 'Front',
            'action' => 'index'
        ] + $config;

        // set base
        parent::__construct($config['base']);

        // set rule
        $this->set(
            ['(:bundle)/(:controller)/(:action)', '(:bundle)/(:controller)', '(:bundle)', ''],
            'app/bundles/(:bundle)/controllers/(:Controller)::(:action)',
            'bunldes/(:bundle)/views/(:controller)/(:action).php',
            $config
        );
    }

}