<?php

namespace app\controllers;

class Home
{

    /**
     * www.you.com/helloworld/
     * www.you.com/helloworld/home
     * or www.you.com/helloworld/home/index
     */
    public function index()
    {
        return ['name' => 'world'];
    }

}