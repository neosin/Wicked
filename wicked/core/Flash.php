<?php

namespace wicked\core;

class Flash
{

    /** @var array */
    protected $_data = [];


    /**
     * Init with repository name
     * @param string $repository
     */
    public function __construct($repository = 'wicked.flash')
    {

        // create if not exist
        if(!isset($_SESSION[$repository]))
            $_SESSION[$repository] = [];

        // bind
        $this->_data = &$_SESSION[$repository];
    }


    /**
     * Add flash message
     * $flash->success('Your message !');
     * @param $name
     * @param array $args
     * @throws \InvalidArgumentException
     */
    public function __call($name, array $args = [])
    {
        // minimum args
        if(empty($args[0]))
            throw new \InvalidArgumentException;

        // extract args
        @list($message, $keep_alive) = $args;

        // default keep alive value
        if(!$keep_alive)
            $keep_alive = 1;

        // add message
        $this->_data[$name] = compact('message', 'keep_alive');
    }


    /**
     * Check if message exists
     * isset($flash->success);
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return !empty($this->_data[$name]);
    }


    /**
     * Consume flash message
     * echo $flash->success
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        // message does not exist
        if(!$this->__isset($name))
            return null;

        // get data
        $content = $this->_data[$name]['message'];

        // keep alive ?
        if($this->_data[$name]['keep_alive'] <= 1)
            unset($this->_data[$name]);
        else
            $this->_data[$name]['keep_alive']--;

        return $content;
    }

}