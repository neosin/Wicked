<?php

namespace wicked\core;

use mog\Mog as MogRequest;
use wicked\core\User;
use wicked\dev\Logger;

class Mog extends MogRequest
{

    /** @var \wicked\core\Route */
    public $route;

    /** @var \wicked\dev\Logger */
    public $log;

    /** @var \wicked\core\User */
    public $user;

    /** @var array */
    protected $_ips = ['127.0.0.1', '::1'];


    /**
     * Init with session & logger
     */
    public function __construct()
    {
        parent::__construct();
        $this->log = new Logger();
        $this->user = new User();
    }

    /**
     * Redirect to the specified url
     * @param String $url
     * @param int $code
     */
    public function redirect($url, $code = 200)
    {
        header('Location: ' . $url, true, $code);
    }


    /**
     * Inner redirection
     * @param $url
     * @param int $code
     */
    public function go($url, $code = 200)
    {
        $this->redirect(url($url), $code);
        exit;
    }


    /**
     * Default home page
     */
    public function home()
    {
        $this->go('');
        return true;
    }


    /**
     * Hydrate an object with data
     * @param $object
     * @param array $data
     * @param bool $force
     */
    public function hydrate(&$object, array $data, $force = false)
    {
        foreach($data as $field => $value)
            if($force or (!$force and property_exists($object, $field)))
                $object->{$field} = $value;
    }


    /**
     * Check if dev mode
     * @param $ip
     * @return bool
     */
    public function dev($ip = null)
    {
        // add ip to dev mode
        if($ip)
            $this->_ips[] = $ip;

        return in_array($this->server->remote_addr, $this->_ips);
    }


    /**
     * Add or get flash
     * @param $name
     * @param null $content
     * @return $this|null
     */
    public function flash($name, $content)
    {
        // init
        if(!isset($this->session['wicked.flash']))
            $this->session['wicked.flash'] = [];

        // set
        $this->session['wicked.flash'][$name] = $content;
        return $this;
    }

}