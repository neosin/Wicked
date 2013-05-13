<?php

namespace app\controllers;

class Home
{

    /**
     * @var \wicked\core\bridge\Mog
     * @context wicked.mog
     */
    public $mog;


    /**
     * Welcome page
     */
    public function index()
    {

    }


    /**
     * For url : /home/hello/{yourname}
     * @param $name
     * @return array
     */
    public function hello($name)
    {
        return compact('name');
    }

}