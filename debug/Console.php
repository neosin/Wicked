<?php

namespace wicked\debug;

use mog\data\Log;
use mog\data\Span;

class Console
{

    /** @var bool */
    protected $_running = true;

    /** @var int */
    protected $_start = 0;

    /** @var int */
    protected $_end = 0;

    /** @var array */
    protected $_logs = [];

    /** @var array */
    protected $_spans = [];

    /** @var string */
    protected $_user = 'all';


    /**
     * Start console
     */
    public function __construct()
    {
        $this->_start = microtime(true);
    }


    /**
     * Add normal log
     * @param $content
     * @param null $data
     * @return $this
     */
    public function log($content, $data = null)
    {
        $this->_write('log', $content, $data);
        return $this;
    }


    /**
     * Add warning log
     * @param $content
     * @param null $data
     * @return $this
     */
    public function warning($content, $data = null)
    {
        $this->_write('warning', $content, $data);
        return $this;
    }


    /**
     * Add error log
     * @param $content
     * @param null $data
     * @return $this
     */
    public function error($content, $data = null)
    {
        $this->_write('error', $content, $data);
        return $this;
    }


    /**
     * Callback execution monitoring
     * @param callable $action
     * @return $this
     */
    public function monitor(callable $action)
    {
        //securize
        if(!$this->_running)
            return $this;

        // start
        $span = new Span();
        $span->start = microtime(true);

        // execute
        $result = call_user_func($action);

        // end
        $span->end = microtime();

        // get backtrace
        $stack = debug_backtrace();
        $span->name = @$stack[1]['file'] . ':' . @$stack[1]['line'];

        // push span
        $this->_spans[$this->_user] = $span;

        // end
        $this->_user = 'all';
        return $result;
    }


    /**
     * Select user
     * @param $user
     * @return $this
     */
    public function __get($user)
    {
        $this->_user = $user;
        return $this;
    }


    /**
     * Write log
     * @param $level
     * @param $content
     * @param null $data
     * @return $this
     */
    protected function _write($level, $content, $data = null)
    {
        // securize
        if(!$this->_running)
            return $this;

        // write
        $this->_logs[$this->_user] = new Log($level, $content, $data);
        $this->_user = 'all';
    }


    /**
     * Get logs and monitoring
     * @return array
     */
    public function get()
    {
        return [
            'logs' => $this->_logs[$this->_user],
            'monitor' => $this->_spans[$this->_user]
        ];
    }


    /**
     * Turn the Console off
     */
    public function off()
    {
        $this->_running = false;
    }


    /**
     * Close console
     */
    public function __destruct()
    {
        $this->_end = microtime(true);
    }

}