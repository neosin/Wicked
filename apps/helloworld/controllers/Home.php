<?php

namespace app\controllers;

class Home
{

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