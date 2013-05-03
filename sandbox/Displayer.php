<?php

namespace wicked\sandbox;

class Renderer
{

    /** @var array */
    protected $_formats = [];

    /** @var string */
    protected $_current = 'default';


    /**
     * Create renderer with default output
     */
    public function __construct()
    {
        $this->set('default', function(array $data = [], $context = null) {
            print_r($data);
        });
    }


    /**
     * Add output format
     * @param $format
     * @param callable $filter
     */
    public function set($format, callable $filter)
    {
        $this->_formats[$format] = $filter;
    }


    /**
     * Specify output format
     * @param $format
     */
    public function __get($format)
    {
        $this->_current = $format;
    }


    /**
     * Render data
     * @param array $data
     * @param null $context
     */
    public function out(array $data = [], $context = null)
    {
        // get function
        $fn = $this->_formats[$this->_current];

        // append context to data
        if($context)
            array_push($data, $context);

        // output
        call_user_func_array($fn, $data);

        // reset current format
        $this->_current = 'default';
    }

}