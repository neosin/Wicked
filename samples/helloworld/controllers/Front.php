<?php

namespace app\controllers;

class Front
{

    use \wicked\tools\wire\Mog;

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