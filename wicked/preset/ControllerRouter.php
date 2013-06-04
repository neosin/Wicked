<?php

namespace wicked\preset;

use wicked\core\Router;

class ControllerRouter extends Router
{

    public function __construct(array $config = [])
    {
        // default config
        $config = [
            'base' => null,
            'controller' => 'Front',
            'action' => 'index',
            'bundle' => null
        ] + $config;

        // set base
        parent::__construct($config['base']);

        // set normal rule
        if(!$config['bundle']) {

            $this->set(
                ['(:controller)/(:action)', '(:controller)', ''],
                'app/controllers/(:Controller)::(:action)',
                'views/(:controller)/(:action).php',
                $config
            );

        }

        // set bundle rule
        else {

            $this->set(
                ['(:controller)/(:action)', '(:controller)', ''],
                'app/bundles/' . $config['bundle'] . '/controllers/(:Controller)::(:action)',
                'bundles/' . $config['bundle'] . 'views/(:controller)/(:action).php',
                $config
            );

        }

    }

}