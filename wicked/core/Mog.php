<?php

namespace wicked\core;

use mog\Mog as MogRequest;
use wicked\core\User;
use wicked\debug\Logger;

class Mog extends MogRequest
{

    /** @var \wicked\core\router\Route */
    public $route;

    /** @var \wicked\debug\Logger */
    public $log;

    /** @var \wicked\core\User */
    public $user;


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
        redirect(url($url), $code);
        exit;
    }


    /**
     * Default home page
     */
    public function home()
    {
        go('');
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

}