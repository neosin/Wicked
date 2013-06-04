<?php

namespace wicked\core;

use wicked\core\Route;

/**
 * Class ExtendedRouter
 * @package wicked\preset\router
 */
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
        foreach((array)$urls as $url) {

            // format pattern
            $pattern = preg_replace('(\(:([a-zA-Z]+)\))', '(?<${1}>[a-zA-Z_]+)', $url); // segments
            $pattern = preg_replace('(\(\+([a-zA-Z]+)\))', '(?<arg_${1}>.+)', $pattern); // forced args

            // add base
            if($this->_base)
                $pattern = $this->_base . '/' . $pattern;

            // slashes
            $pattern = str_replace('/', '\/', $pattern);

            // args
            $pattern .= '(?<args>(\/[a-zA-Z0-9]+)*)';

            // wrap
            $pattern = '/^' . $pattern . '$/';

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
        // clean url
        $request = rtrim($request, '/');

        // look in all routes
        foreach($this->_routes as $pattern => $route) {

            // found !
            if(preg_match($pattern, $request, $data)) {

                // init
                $forced = [];

                // clean placeholders
                foreach($data as $key => $value) {

                    // remove int key
                    if(is_int($key)) {
                        unset($data[$key]);
                    }

                    // parse forced args
                    elseif(substr($key, 0, 4) == 'arg_') {
                        $forced[] = $value;
                        unset($data[$key]);
                    }

                }

                // parse args
                $args = empty($data['args']) ? [] : explode('/', trim($data['args'], '/'));
                $data['args'] = array_merge($forced, $args);

                $route->data = $data;
                return $route;
            }

        }

        return false;
    }


    /**
     * Format route from pattern matching
     * @param \wicked\core\Route $route
     * @return \wicked\core\Route
     */
    public function resolve(Route $route)
    {
        // add args
        $route->args = $route->data['args'];
        unset($route->data['args']);

        // apply filter
        if(is_callable($route->filter))
            $route->data = call_user_func($route->filter, $route->data);

        // default values
        if($route->defaults)
            $route->data += $route->defaults;

        // duplicate data with ucfirst
        foreach($route->data as $key => $value)
            $route->data[ucfirst($key)] = ucfirst($value);

        // resolve action and view
        foreach($route->data as $key => $value) {

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
        if($route = $this->match($request))
            return $this->resolve($route);

        return false;
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


}