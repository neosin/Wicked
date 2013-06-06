<?php

namespace wicked\core\helper;

class Session
{

    /** @var string */
    protected $_repository;

    /** @var bool */
    protected $_silent;


    /**
     * Bind to repository
     * @param $repository
     * @param bool $silent
     */
    public function Session($repository, $silent = true)
    {
        $this->_repository = $repository;
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
        if(isset($_SESSION[$this->_repository][$name])) {

            // get data
            $data = $_SESSION[$this->_repository][$name];

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
        $_SESSION[$this->_repository][$name] = is_scalar($value) ? $value : serialize($value);
    }


    /**
     * Clear all session
     */
    public function clear($name = null)
    {
        if($name) {

            // unset data
            if(isset($_SESSION[$this->_repository][$name]))
                unset($_SESSION[$this->_repository][$name]);

            // data does not exist
            elseif(!$this->_silent)
                throw new \InvalidArgumentException('Key [' . $name . '] does not exists in session.');

            // silent mode
            else
                return null;

        }

        // remove all
        $_SESSION[$this->_repository] = [];
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