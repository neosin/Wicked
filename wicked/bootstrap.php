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
 * @return mixed|\wicked\core\helper\Session
 */
function session($name = null, $value = null)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\helper\Session('wicked.session');

    // case 1 : get object
    if(!$name)
        return $session;

    // case 2 : get value
    elseif($name and !$value)
        return $session->get($name);

    // case 3 : set value
    $session->set($name, $value);
}


/**
 * Flash helper
 * @param $name
 * @param null $value
 * @return mixed|\wicked\core\helper\Session
 */
function flash($name, $value = null)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\helper\Session('wicked.flash');

    // case 1 : get flash
    if(!$value) {
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
 * @return mixed|\wicked\core\helper\Session
 */
function user($key, $value = null)
{
    static $session;

    // create session entity
    if(!$session)
        $session = new wicked\core\helper\Session('wicked.user');

    // case 1 : get entity
    if(!$key)
        return $session->get('entity');

    // case 2 : get value
    elseif($key and !$value)
        return $session->get($key);

    // case 3 : set value
    $session->set($key, $value);
}