<?php

namespace wicked\core;

class Session
{

    /** @var string */
    protected $_root;

    /** @var bool */
    protected $_silent;


    /**
     * Bind to repository
     * @param $repository
     * @param bool $silent
     */
    public function __construct($repository, $silent = true)
    {
        $this->_root = $repository;
        $this->_silent = $silent;
    }


    /**
     * Get value
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if(isset($_SESSION[$this->_root][$name])) {

            // get data
            $data = $_SESSION[$this->_root][$name];

            // unserialize
            return static::serialized($data)
                ? unserialize($data)
                : $data;
        }
        elseif(!$this->_silent)
            throw new \InvalidArgumentException('Key [' . $name . '] does not exists in session.');
    }


    /**
     * Set value
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $_SESSION[$this->_root][$name] = is_scalar($value) ? $value : serialize($value);
    }


    /**
     * Clear all session
     */
    public function clear($name = null)
    {
        if($name) {

            // unset data
            if(isset($_SESSION[$this->_root][$name]))
                unset($_SESSION[$this->_root][$name]);

            // data does not exist
            elseif(!$this->_silent)
                throw new \InvalidArgumentException('Key [' . $name . '] does not exists in session.');

            // silent mode
            else
                return null;

        }

        // remove all
        $_SESSION[$this->_root] = [];
    }


    /**
     * Check if string is serialized
     * @param $string
     * @return bool
     */
    protected static function serialized($string)
    {
        return (@unserialize($string) !== false and $string !== 'b:0;');
    }

}