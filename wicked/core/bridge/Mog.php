<?php

namespace wicked\core\bridge;

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

}