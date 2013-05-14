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
namespace wicked\dev\data;

class Log
{

    /** @var float */
    public $time;

    /** @var string */
    public $level;

    /** @var string */
    public $message;

    /** @var mixed */
    public $data;


    /**
     * Create log
     * @param $level
     * @param $message
     * @param null $data
     */
    public function __construct($level, $message, $data = null)
    {
        $this->level = $level;
        $this->message = $message;
        $this->data = $data;
        $this->time = microtime(true);
    }

}