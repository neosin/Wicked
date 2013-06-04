<?php

namespace wicked\tools\crud;

trait Read
{

    use \wicked\wire\All;

    /** @var string */
    public $model;

    /**
     * Display entity
     * @param $id
     * @return array
     */
    public function read($id)
    {
        // parse
        $key = \wicked\tools\Inflector::classname($this->model);

        // get entity
        if($entity = $this->syn->{$key}->find($id)) {
            return [$key => $entity];
        }

        // entity not found
        $this->mog->oops($key . ' [' . $id . '] not found.', 404);
    }

}