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
namespace wicked\core\session;

use wicked\core\Session;

class User extends Session
{

    /** @var int */
    public $rank = 0;

    /** @var array */
    public $flash = [];

    /** @var  */
    public $user;


    /**
     * @param $key
     */
    public function __construct($key)
    {
        // constructor
        parent::__construct($key);

        // get statefull data
        $this->rank = $this->__get('rank');
        $this->flash = $this->__get('flash');
        $this->user = $this->__get('user');
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
        $this->__set('rank', $this->rank);
        $this->__set('flash', $this->flash);
        $this->__set('user', $this->user);
    }

}
