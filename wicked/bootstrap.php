<?php
/**
 * This file is part of the Wicked package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * - - - - - - - - - - - - - -
 *
 * Bootstrat : DO NOT CHANGE ANYTHING !!
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-02
 * @version 0.1
 */

/*
 * Setup autoloader
 */
require 'core/Loader.php';
spl_autoload_register('wicked\core\Loader::load');

/**
 * Register framework and app path
 */
wicked\core\Loader::register('wicked', dirname(__FILE__));
wicked\core\Loader::register('app', dirname($_SERVER['SCRIPT_FILENAME']));

/**
 * Register libs
 */
wicked\core\Loader::register('psr', dirname(__FILE__) . '/libs/psr/');
wicked\core\Loader::register('mog', dirname(__FILE__) . '/libs/mog/');
wicked\core\Loader::register('maestro', dirname(__FILE__) . '/libs/maestro/');
wicked\core\Loader::register('syn', dirname(__FILE__) . '/libs/syn/');

/*
 * Define base url
 */
define('APP_URL', dirname($_SERVER['SCRIPT_NAME']) . '/');

/*
 * Init session
 */
session_start();


/*
 * Functions
 */

/**
 * Get registered path
 * @param $path
 * @return string
 */
function path($path)
{
    return wicked\core\Loader::path($path);
}


/**
 * Get complete url
 * @param $path
 * @return string
 */
function url($path)
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}


/**
 * Redirect to the specified url
 * @param String $url
 * @param int $code
 */
function redirect($url, $code = 200)
{
    header('Location: ' . $url);
}


/**
 * Inner redirection
 * @param $url
 * @param int $code
 */
function go($url, $code = 200)
{
    redirect(url($url), $code);
    exit;
}


/**
 * Default home page
 */
function home()
{
    go('');
    return true;
}


/**
 * Shortcut : dev mode
 * @return bool
 */
function dev()
{
    return wicked\debug\Env::dev();
}


/**
 * Die var_dump
 */
function debug()
{
    die(call_user_func_array('var_dump', func_get_args()));
}


/**
 * Hydrate an object with data
 * @param $object
 * @param array $data
 * @param bool $force
 */
function hydrate(&$object, array $data, $force = false)
{
    foreach($data as $field => $value)
        if($force or (!$force and property_exists($object, $field)))
            $object->{$field} = $value;
}


/**
 * Get the first element of an array
 * @param array $array
 * @return bool
 */
function first(array &$array)
{
    return isset($array[0]) ? $array[0] : false;
}


/**
 * Check if a var exists and not null
 * @param $var
 * @return bool
 */
function exists($var)
{
    return !empty($var);
}