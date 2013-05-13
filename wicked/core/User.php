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
namespace wicked\core;

class User
{

    /** @var int */
    public $rank = 0;

    /** @var object */
    public $entity;


    /**
     * Init with session
     */
    public function __construct()
    {
        if(!empty($_SESSION['wicked.user'])) {

            $this->rank = $_SESSION['wicked.user']['rank'];
            $this->entity = unserialize($_SESSION['wicked.user']['entity']);

        }
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
        $_SESSION['wicked.user']['entity'] = serialize($this->entity);
    }

}
