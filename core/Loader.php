<?php

namespace wicked\core;

abstract class Loader
{

    /** @var array */
    protected static $_path = [];


    /**
     * Register a root path
     * @param $prefix
     * @param $path
     */
    public static function register($prefix, $path)
    {
        $prefix = trim($prefix, '\\');
        $path = str_replace('\\', DIRECTORY_SEPARATOR , $path);
        $path = str_replace('_', DIRECTORY_SEPARATOR , $path);
        static::$_path[$prefix] = $path . '/';
    }


    /**
     * Load a class
     * @param $namespace
     * @return bool
     */
    public static function load($namespace)
    {
        // split namespace
        $forpath = $namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $exp = explode(DIRECTORY_SEPARATOR, $namespace);

        // get prefix
        $prefix = array_shift($exp);

        // uppercase Classname
        array_push($exp, ucfirst(array_pop($exp)));

        // re-build namespace
        $namespace = implode(DIRECTORY_SEPARATOR, $exp);

        // search registered path
        if(array_key_exists($prefix, static::$_path))
        {
            // build file path
            $path = static::$_path[$prefix] . $namespace . '.php';

            // file exists ?
            if(file_exists($path))
            {
                require_once $path;
                return true;
            }
        }

        return false;
    }


    /**
     * @param $key
     * @return mixed
     */
    public static function path($key)
    {
        if(!array_key_exists($key, static::$_path))
            die('Loader ['.$key.'] does not exists.');

        return static::$_path[$key];
    }

}