<?php

namespace wicked\debug\data;

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