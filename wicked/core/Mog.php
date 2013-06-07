<?php

namespace wicked\core;

use mog\Mog as MogRequest;
use wicked\core\User;
use wicked\dev\Logger;

class Mog extends MogRequest
{

    /** @var \wicked\core\Route */
    public $route;

}