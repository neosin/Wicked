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

class Session
{

    /** @var string */
    protected $_key;


    /**
     * Open session
     * @param $key
     */
    public function __construct($key)
    {
        $this->_key = $key;

        if(!isset($_SESSION[$key]))
            $_SESSION[$key] = [];
    }


    /**
     * Get value
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return isset($_SESSION[$this->_key][$name])
            ? unserialize($_SESSION[$this->_key][$name])
            : null;
    }


    /**
     * Set value
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $_SESSION[$this->_key][$name] = serialize($value);
    }


    /**
     * Clear session
     */
    public function clear()
    {
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id(true);
    }

}
