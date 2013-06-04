<?php

namespace wicked\preset;

use wicked\core\Router;

class BundleRouter extends Router
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
                ['(:action)', ''],
                'app/controllers/' . $config['controller'] . '::(:action)',
                'views/' . ucfirst($config['controller']) . '/(:action).php',
                $config
            );

        }

        // set bundle rule
        else {

            $this->set(
                ['(:action)', ''],
                'app/bundles/' . $config['bundle'] . '/controllers/' . $config['controller'] . '::(:action)',
                'bundles/' . $config['bundle'] . '/views/' . ucfirst($config['controller']) . '/(:action).php',
                $config
            );

        }

    }

}