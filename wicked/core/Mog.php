<?php

namespace wicked\core;

use mog\Mog as MogRequest;
use wicked\core\User;
use wicked\dev\Logger;

class Mog extends MogRequest
{

    /** @var \wicked\core\Route */
    public $route;

    /** @var int */
    public $rank;

    /** @var mixed */
    public $user;

    /** @var array */
    protected $_ips = ['127.0.0.1', '::1'];


    /**
     * Init with session & logger
     */
    public function __construct()
    {
        parent::__construct();

        // retrieve session user
        if(!empty($_SESSION['wicked.user'])) {
            $this->rank = $_SESSION['wicked.user']['rank'];
            $this->user = unserialize($_SESSION['wicked.user']['entity']);
        }

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


    /**
     * Login user
     * @param $rank
     * @param null $user
     */
    public function login($rank = 1, $user = null)
    {
        $this->rank = $rank;
        $this->user = $user;
    }


    /**
     * Logout user
     */
    public function logout()
    {
        $this->rank = 0;
        $this->user = null;
    }


    /**
     * Save user session
     */
    public function __destruct()
    {
        $_SESSION['wicked.user']['rank'] = $this->rank;
        $_SESSION['wicked.user']['entity'] = serialize($this->user);
    }

}