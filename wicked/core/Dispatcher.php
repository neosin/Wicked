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

use wicked\core\Wire;
use wicked\tools\Annotation;

class Dispatcher
{

    use Events;

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
     * @param \wicked\core\Mog $request
     * @throws \Exception
     * @return array|bool
     */
    public function route(Mog $request)
    {
        // event before.route
        $this->fire('before.route', [&$this, &$request]);

        $route = $this->router->find($request->server->query_string);

        if(!$route)
            throw new \Exception('Route [' . $request->server->query_string . '] not found', 404);

        // event after.route
        $this->fire('after.route', [&$this, &$route]);

        return $route;
    }


    /**
     * Set response
     * @param \wicked\core\Route $route
     * @return mixed
     */
    public function build(Route $route)
    {
        // event before.build
        $this->fire('before.build', [&$this, &$route]);

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
            $wire = new Wire();
            $wire->apply($build[0]);
        }

        // action not found
        if(!is_callable($build))
            throw new \RuntimeException('Action [' . (is_array($build) ? get_class($build[0]) . '::' . $build[0] : $build) . '] not found', 404);

        // event build
        $this->fire('build', [&$this, &$build, &$route]);

        // run action
        $output = call_user_func_array($build, $route->args);

        // event before.build
        $this->fire('after.build', [&$this, &$output]);

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