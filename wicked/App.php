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
namespace wicked;

use wicked\core\Mog;
use wicked\core\Router;
use wicked\core\Kernel;
use wicked\core\View;
use wicked\tools\Annotation;
use wicked\preset\ControllerRouter;
use maestro\Registrar;

/**
 * App class, gather Kernel (action side) and Render (view side)
 */
class App extends Kernel implements \ArrayAccess
{

    /**
     * @param core\Router $router
     */
    public function __construct(Router $router = null)
    {
        // default router
        $router = $router ?: new ControllerRouter();

        // setup kernel
        parent::__construct($router);

        // auto register mog as dependency
        $this['mog'] = $this->mog;

        // flash support
        $this->on('render', function($app, \wicked\core\View $view){
            $view->set('_flash', $app->mog->flash);
        });

        // auth filter & customer view
        $this->on('build', function($app, $build, $route){

            // only for controller::method
            if(is_array($build)) {

                // init security
                $allowed = true;

                // get rank annotation on method
                $rank = Annotation::method($build[0], $build[1], 'rank');

                // method defines rank, check security
                if($rank != null and $rank > $app->mog->user->rank)
                    $allowed = false;

                // method does not defines rank
                if($rank == null) {

                    // get rank annotation on controller
                    $rank = Annotation::object($build[0], 'rank');

                    // check
                    if($rank and $rank > $app->mog->user->rank)
                        $allowed = false;

                }

                // allowed ?
                if(!$allowed)
                    $this->mog->oops('Action [' . get_class($build[0]) . '::' . $build[1] . '] not allowed', 403);

                // custom view
                $view = Annotation::method($build[0], $build[1], 'view');
                if($view !== null)
                    $app->mog->route->view = $view;
            }

        });
    }


    /**
     * Run this awesome app !
     * @param \wicked\core\Mog $force
     * @return mixed|void
     */
    public function run(Mog $force = null)
    {
        // run kernel
        try
        {
            // event before.run
            $this->fire('before.run', [&$this, &$force]);

            // run kernel
            $data = parent::run($force);

            // format array
            if($data !== false)
                $this->render($data, $this->mog->route->view);

            // event after.run
            $this->fire('after.run', [&$this]);
        }
        catch(\Exception $e)
        {
            // trigger event
            $has = $this->fire($e->getCode(), [&$this, $e->getMessage()]);

            // no listener for this event
            if(!$has)
                $this->mog->oops($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Render data
     * @param $data
     * @param $template
     * @return bool
     */
    public function render($data, $template)
    {
        // event before.render
        $this->fire('before.render', [&$this, &$data, &$template]);

        $view = new View($template, $data ?: []);

        // event : render
        $this->fire('render', [&$this, &$view]);

        echo $view->display();

        // event : render
        $this->fire('after.render', [&$this]);

        return false; // stop upcoming render
    }


    /**
     * Get service
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        // event set.service
        $this->fire('set.service', [&$this, $key, $value]);

        Registrar::register('wicked.' . $key, $value);
    }


    /**
     * Store service
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        // event get.service
        $this->fire('set.service', [&$this, $key]);

        return Registrar::run('wicked.' . $key);
    }


    /**
     * Check if a service is stored
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return Registrar::exists('wicked' . $key);
    }


    /**
     * Nothing
     * @param mixed $key
     */
    public function offsetUnset($key){}


    /**
     * Thanks for your support :)
     */
    public function __toString()
    {
        return str_replace('http://', '', APP_URL) . ' powered by Wicked ;)';
    }

}
