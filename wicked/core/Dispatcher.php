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
use wicked\core\bridge\ContextWire;

class Dispatcher
{

    /** @var Router */
    public $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    /**
     * Set action
     * @param \wicked\core\bridge\Mog $request
     * @throws \Exception
     * @return array|bool
     */
    public function route(Mog $request)
    {
        $route = $this->router->find($request->server->query_string);

        if(!$route)
            throw new \Exception('Route [' . $url . '] not found', 404);

        return $route;
    }


    /**
     * Set response
     * @param \wicked\core\router\Route $route
     * @param router\Route $route
     * @return mixed
     */
    public function build(Route $route)
    {
        $build = $route->action;

        // string based action (Class::method) ?
        if(is_string($build) and !function_exists($build))
        {
            // extract ns and method
            list($class, $method) = explode('::', $build);

            // build action
            $class = str_replace('/', '\\', $class);
            $build = [new $class(), $method];
        }

        // apply auto-wiring on method
        if(is_array($build))
        {
            $wire = new ContextWire();
            $wire->apply($build[0]);
        }

        // run action
        $output = call_user_func_array($build, $route->args);

        return $output;
    }


    /**
     * Global run
     * @param Mog $request
     * @return mixed
     */
    public function run(Mog $request)
    {
        $route = $this->route($request);
        $output = $this->build($route);

        return $output;
    }

}