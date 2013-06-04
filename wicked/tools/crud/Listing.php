<?php

namespace wicked\tools\crud;

trait Listing
{

    use \wicked\wire\All;

    /** @var string */
    public $model;

    /**
     * Display entity
     * @return array
     */
    public function index()
    {
        // parse
        $key = \wicked\tools\Inflector::classname($this->model);

        // get all
        $listing = $this->syn->{$key}->find();

        return ['listing' => $listing];
    }

}