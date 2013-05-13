<?php

/**
 * This file is part of the Wicked package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-02
 * @version 0.1
 */
namespace wicked\core;

use wicked\core\router\Route;
use wicked\core\bridge\Mog;

class Router
{

    /** @var string */
    protected $_base;

    /** @var array */
    protected $_routes = [];


    /**
     * Create a new router with its components
     * @param string $base
     */
    public function __construct($base = '')
    {
        $this->_base = trim($base, '/');
    }


    /**
     * Add and format a new route
     * @param $urls
     * @param string $action
     * @param string $view
     * @param array $defaults
     * @param callable $filter
     * @return \wicked\core\Router
     */
    public function set($urls, $action, $view = null, array $defaults = [], \Closure $filter = null)
    {
        foreach((array)$urls as $url)
        {
            // format pattern
            $pattern = static::_format($url);

            // register
            $route = new Route($url, $action, $view, $defaults, $filter);
            $this->_routes[$pattern] = $route;
        }

        return $this;
    }


    /**
     * Find if a route exists from request
     * @param $request
     * @return array|bool
     */
    public function match($request)
    {
        foreach($this->_routes as $pattern => $route)
        {
            if(preg_match($pattern, $request, $placeholders))
            {
                $placeholders['args'] = static::_parseArgs($placeholders['args']);
                $placeholders = static::_cleanData($placeholders);

                return [$route, $placeholders];
            }
        }

        return false;
    }


    /**
     * Format route from pattern matching
     * @param router\Route $route
     * @param array $placeholders
     * @return router\Route
     */
    public function resolve(Route $route, array $placeholders)
    {
        // add args
        $route->args = $placeholders['args'];
        unset($placeholders['args']);

        // apply filter
        if(is_callable($route->filter))
            $placeholders = call_user_func($route->filter, $placeholders);

        // default values
        if($route->defaults)
            $placeholders += $route->defaults;

        // resolve action and view
        foreach($placeholders as $key => $value)
        {
            // resolve action
            $route->action = str_replace('(:'.$key.')', $value, $route->action);
            $route->view = str_replace('(:'.$key.')', $value, $route->view);
        }

        // return formatted route
        return $route;
    }


    /**
     * Main process
     * @param $request
     * @return array|bool
     */
    public function find($request)
    {
        // requet matching
        if($match = $this->match($request))
        {
            // get route
            list($route, $placeholders) = $match;

            // get route
            return $this->resolve($route, $placeholders);
        }

        return false;
    }


    /**
     * Format pattern with placeholers
     * Format pattern
     * @param $pattern
     * @return string
     */
    protected function _format($pattern)
    {
        // keys
        $pattern = preg_replace('(\(:([a-zA-Z0-9]+)\))', '(?<${1}>[a-zA-Z-_]+)', $pattern); // old version : (?<${1}>[a-zA-Z0-9-_]+)

        // add base
        if($this->_base)
            $pattern = $this->_base . '/' . $pattern;

        // slashes
        $pattern = str_replace('/', '\/', $pattern);

        // args
        $pattern .= '(?<args>(\/[a-zA-Z0-9]+)*)';

        // wrap
        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }


    /**
     * Get all routes
     * @return array
     */
    public function routes()
    {
        return $this->_routes;
    }


    /**
     * Add routes from another router
     * @param Router $router
     */
    public function bind(Router $router)
    {
        $this->_routes += $router->routes();
    }


    /**
     * Clean output data
     * @param array $data
     * @return array
     */
    protected static function _cleanData(array $data)
    {
        foreach($data as $k => $v)
            if(is_int($k))
                unset($data[$k]);

        return $data;
    }


    /**
     * Parse arguments
     * @param array $args
     * @return array
     */
    protected static function _parseArgs($args)
    {
        // parse
        $trim = trim($args, '/');

        // default if empty
        $args = empty($trim) ? [] : explode('/', $trim);

        return $args;
    }




    /*
     * Preset
     */


    /**
     * Provide an auto-configured simple router
     * @return Router
     */
    public static function simple()
    {
        $router = new Router();

        $router->set(
            ['(:action)', ''],
            'app/controllers/Home::(:action)',
            'views/home/(:action).php',
            ['action' => 'index']
        );

        return $router;
    }


    /**
     * Provide an auto-configured classic router
     * @return Router
     */
    public static function classic()
    {
        $router = new Router();

        $router->set(
            ['(:controller)/(:action)', '(:controller)', ''],
            'app/controllers/(:controller)::(:action)',
            'views/(:controller)/(:action).php',
            ['controller' => 'Home', 'action' => 'index']
        );

        return $router;
    }


    /**
     * Provide an auto-configured bundle router
     * @return Router
     */
    public static function bundle()
    {
        $router = new Router();

        $router->set(
            ['(:bundle)/(:controller)/(:action)', '(:bundle)/(:controller)', '(:bundle)', ''],
            'app/bundles/(:bundle)/controllers/(:controller)::(:action)',
            'bundles/(:bundle)/views/(:controller)/(:action).php',
            ['bundle' => 'default', 'controller' => 'Home', 'action' => 'index']
        );

        return $router;
    }

}