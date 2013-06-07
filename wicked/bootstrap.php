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
session_set_cookie_params(604800);
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
 * Redirect to url
 * @param $url
 */
function go($url)
{
    header('Location: ' . url($url));
    exit;
}


/**
 * Debug var
 */
function debug()
{
    die(call_user_func_array('var_dump', func_get_args()));
}


/**
 * Session helper
 * @param null $name
 * @param null $value
 * @return mixed|\wicked\core\Session
 */
function session($name = null, $value = null)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\Session('wicked.session');

    // case 1 : get object
    if(!$name)
        return $session;

    // case 2 : get value
    elseif($name and is_null($value))
        return $session->get($name);

    // case 3 : set value
    $session->set($name, $value);
}


/**
 * Flash helper
 * @param $name
 * @param null $value
 * @return string|\wicked\core\Session
 */
function flash($name, $value = null)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\Session('wicked.flash');

    // case 1 : get flash
    if(is_null($value)) {
        $message = $session->get($name);
        $session->clear($name);
        return $message;
    }

    // case 2 : set flash
    $session->set($name, $value);
}


/**
 * User helper
 * @param $key
 * @param null $value
 * @return mixed|\wicked\core\Session
 */
function user($key = null, $value = -1)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\Session('wicked.user');

    // case 1 : get entity
    if(!$key)
        return $session->get('entity');

    // case 2 : set value
    elseif($value == -1)
        return $session->get($key);

    // case 3 : set value
    else
        $session->set($key, $value);
}


/**
 * Log in and out
 * @param $entity
 * @param int $rank
 */
function auth($entity, $rank = 1)
{

    // login
    if($entity) {
        user('entity', $entity);
        user('rank', $rank);
    }
    else {
        user('entity', null);
        user('rank', null);
    }

}


/**
 * Post helper
 * @param null $key
 * @return null
 */
function post($key = null)
{
    if(!$key)
        return $_POST;

    return isset($_POST[$key]) ? $_POST[$key] : null;
}


/**
 * Throw error
 * @param $message
 * @param int $code
 * @throws Exception
 */
function oops($message, $code = 0)
{
    throw new \Exception($message, $code);
}


/**
 * Hydrate object with data
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
 * Forward action without redirect
 * todo
 */
function forward($action)
{
    // get app
    $app = \maestro\Registrar::run('wicked.app');

    // reverse routing
    $route = $app->router->reverse($action);

    // update mog
    $mog = mog();
    $mog->route = $route;

    // reload app
    $app->reload($mog);

    exit;
}


/**
 * Mog helper
 * return \wicked\core\Mog
 */
function mog()
{
    return \maestro\Registrar::run('wicked.mog');
}


/**
 * Syn helper
 * @return \syn\core\ORM
 */
function syn()
{
    return \maestro\Registrar::run('wicked.syn');
}