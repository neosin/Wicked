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
        header('Location: ' . $url);
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
     * Add or get flash
     * @param $name
     * @param null $content
     * @param int $iteration
     * @return $this|null
     */
    public function flash($name, $content, $iteration = 1)
    {
        // init
        if(!isset($this->session['wicked.flash']))
            $this->session['wicked.flash'] = [];

        // set
        $this->session['wicked.flash'][$name] = [
            'content' => $content,
            'used' => 0,
            'max' => $iteration
        ];

        return $this;
    }

}