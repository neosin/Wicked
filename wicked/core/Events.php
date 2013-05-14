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

trait Events
{

    /** @var array list of events */
    protected $_events = [];

    /** @var array list of static events */
    protected static $_staticEvents = [];


    /**
     * Listen event
     * @param $event
     * @param $callback
     */
    public function on($event, callable $callback)
    {
        // create rep if not exists
        if(!isset($this->_events[$event]))
            $this->_events[$event] = [];

        // add event
        $this->_events[$event][] = $callback;
    }


    /**
     * Listen static event
     * @param $event
     * @param callable $callback
     */
    public static function listen($event, callable $callback)
    {
        // create rep if not exists
        if(!isset(static::$_staticEvents[$event]))
            static::$_staticEvents[$event] = [];

        // add event
        static::$_staticEvents[$event][] = $callback;
    }


    /**
     * Fire event
     * @param $event
     * @param array $args
     */
    public function fire($event, $args = [])
    {
        // init
        $done = 0;

        // cast array
        if(!is_array($args))
            $args = [$args];

        // inner events
        if(!empty($this->_events[$event]))
        {
            foreach($this->_events[$event] as $e) {
                call_user_func_array($e, $args);
                $done++;
            }
        }

        // static events
        if(!empty(static::$_staticEvents[$event]))
        {
            foreach(static::$_staticEvents[$event] as $e) {
                call_user_func_array($e, $args);
                $done++;
            }
        }

        return $done;
    }

}