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
use wicked\core\Displayer;
use maestro\Registrar;

/**
 * App class, gather Kernel (action side) and Render (view side)
 */
class App extends Kernel
{

    /** @var \wicked\core\Displayer */
    public $displayer;

    /**
     * @param core\Router $router
     * @param core\Displayer $render
     */
    public function __construct(Router $router = null, Displayer $displayer = null)
    {
        // default router
        $router = $router ?: Router::classic();

        // setup kernel
        parent::__construct($router);

        // assign render
        $this->displayer = $displayer ?: new Displayer();

        // auto register as dependecy
        $this->set('app', $this);
        $this->set('mog', $this->mog);
    }


    /**
     * Register object as dependency
     * @param $key
     * @param $object
     * @return $this
     */
    public function set($key, &$object)
    {
        Registrar::register('wicked.' . $key, $object);
        return $this;
    }


    /**
     * Get dependecy
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return Registrar::run($key);
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
            // run kernel
            $data = parent::run($force);

            // format array
            if($data !== false)
                $this->render([$data, $this->route], $this->displayer->format);
        }
        catch(\Exception $e)
        {
            // trigger event
            $has = $this->fire($e->getCode());

            // no listener for this event
            if(!$has)
                $this->mog->oops($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Render data
     * @param $data
     * @param string $format
     * @return bool
     */
    public function render($data, $format = 'html')
    {
        $this->displayer->out($data, $format);
        return false; // stop upcoming render
    }


    /**
     * Thanks for your support :)
     */
    public function __toString()
    {
        return str_replace('http://', '', APP_URL) . ' powered by Wicked ;)';
    }

}
