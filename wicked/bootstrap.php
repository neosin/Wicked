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
 * Change view and pass data
 * @param $view
 * @param array $data
 * @return callable
 */
function render($view, $data = [])
{
    return function(\wicked\App &$app) use($view, $data)
    {
        $app->mog->route->view = $view;
        return $data;
    };
}


/**
 * Debug var
 */
function debug()
{
    die(call_user_func_array('var_dump', func_get_args()));
}