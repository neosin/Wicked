<?php

/**
 * This file is part of Mog.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-06
 * @version 1
 */
namespace mog;

/**
 * Class Request
 * @package mog
 *
 * Basic Request object with globals data
 */
class Request
{

    /** @var array */
    public $get = [];

    /** @var array */
    public $post = [];

    /** @var array */
    public $files = [];

    /** @var array */
    public $cookie = [];

    /** @var array */
    public $session = [];

    /** @var \stdClass */
    public $server;

    /** @var \stdClass */
    public $env;

    /** @var \stdClass */
    public $headers;


    /**
     * Create request from globals
     * @param bool $globals
     */
    public function __construct($globals = false)
    {
        if($globals) {

            // user data
            $this->get = &$_GET;
            $this->post = &$_POST;
            $this->files = &$_FILES;
            $this->cookie = &$_COOKIE;

            // session started
            if(isset($_SESSION))
                $this->session = &$_SESSION;

            // env data
            $this->server = (object)array_change_key_case($_SERVER, CASE_LOWER);
            $this->env = (object)array_change_key_case($_ENV, CASE_LOWER);
            $this->headers = (object)array_change_key_case(getallheaders(), CASE_LOWER);
        }
    }

}