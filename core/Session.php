<?php

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
