<?php

/**
 * This file is part of the Maestro package.
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
namespace maestro;

abstract class Registrar
{

    /** @var array */
    protected static $_items = [];

    /** @var array */
    protected static $_locks = [];


    /**
     * Register a new container
     * @param $name
     * @param $class
     * @param array $args
     * @param array $properties
     * @throws \RuntimeException
     */
    public static function register($name, $class, array $args = [], array $properties = [])
    {
        // is locked ?
        if(in_array($name, static::$_locks))
            throw new \RuntimeException('Dependency [' . $name . '] is read-only');

        // store object
        if(is_object($class))
        {
            static::$_items[$name] = $class;
        }
        // store late object
        else
        {
            // create container
            $item = new Wrapper($class);
            $item->args($args);
            $item->properties($properties);

            // store
            static::$_items[$name] = &$item;
        }
    }


    /**
     * Lock a dependecy
     * @param $name
     */
    public static function lock($name)
    {
        static::$_locks[$name] = $name;
    }


    /**
     * Unlock a dependecy
     * @param $name
     */
    public static function unlock($name)
    {
        unset(static::$_locks[$name]);
    }


    /**
     * Check if a dependecy is stored
     * @param $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset(static::$_items[$name]);
    }


    /**
     * Retrieve a container
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function raw($name)
    {
        if(!static::exists($name))
            throw new \InvalidArgumentException('Dependency [' . $name . '] does not exists');

        return static::$_items[$name];
    }


    /**
     * Run a container
     * @param $name
     * @return mixed
     */
    public static function run($name)
    {
        $item = static::raw($name);
        return ($item instanceof Wrapper) ? $item->run() : $item;
    }


    /**
     * Factory builder
     * @param $class
     * @param array $args
     * @return mixed
     */
    public static function factory($class, array $args = [])
    {
        if(static::exists($class))
            return static::run($class);
        else
        {
            $item = new Wrapper($class);
            $item->args($args);
            return $item->run();
        }
    }

}