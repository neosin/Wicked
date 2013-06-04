<?php

namespace app\controllers;

class Front
{

    use \wicked\wire\Mog;

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
        return ['name' => $name];
    }

}