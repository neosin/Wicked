<?php

namespace wicked\core;

use wicked\core\meta\EventManager;
use wicked\core\router\Route;
use wicked\core\view\Template;

class Displayer
{

    /** @var array */
    protected $_formats = [];

    /** @var string */
    public $format = 'html';


    /**
     * Create a new Render engine and setup 2 formats
     */
    public function __construct()
    {
        // raw
        $this->set('raw', function($data){
            echo $data;
        });

        // json
        $this->set('json', function($data){
            echo json_encode($data);
        });

        // template engine
        $this->set('html', function($data, Route $route){
            $view = new Template(strtolower($route->view), (array)$data);
            echo $view->display();
        });
    }


    /**
     * Add a new format
     * @param $name
     * @param $callback
     */
    public function set($name, callable $callback)
    {
        $this->_formats[$name] = $callback;
    }


    /**
     * Use an output format
     * @param $data
     * @param $format
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function out($data, $format = null)
    {
        // set format
        $format = $format ?: $this->format;

        // exists ?
        if(isset($this->_formats[$format]))
        {
            $callback = $this->_formats[$format];
            return call_user_func_array($callback, (array)$data);
        }
        else
            throw new \InvalidArgumentException('Format [' . $format . '] does not exist');
    }

}
