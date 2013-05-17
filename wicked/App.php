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
        $router = $router ?: Router::classic();

        // setup kernel
        parent::__construct($router);

        // auto register as dependecy
        $this['app'] = $this;
        $this['mog'] = $this->mog;
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

        return Registrar::run($key);
    }


    /**
     * Check if a service is stored
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return Registrar::exists($key);
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
