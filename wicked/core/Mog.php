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

    /** @var \wicked\core\Flash */
    public $flash;


    /**
     * Init with session & logger
     */
    public function __construct()
    {
        parent::__construct();

        // create flash message manager
        $this->flash = new Flash('wicked.flash');

        // retrieve session user
        $this->rank = isset($this->cookie['wicked.rank']) ? $this->cookie['wicked.rank'] : 0 ;
        $this->user = isset($this->cookie['wicked.user']) ? unserialize($this->cookie['wicked.user']) : null ;

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
        setcookie('wicked.rank', $this->rank);
        setcookie('wicked.mog', serialize($this->user));
    }

}