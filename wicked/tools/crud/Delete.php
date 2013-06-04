<?php

namespace wicked\tools\crud;

trait Delete
{

    use \wicked\wire\All;

    /** @var string */
    public $model;

    /**
     * Delete entity
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        // parse
        $key = \wicked\tools\Inflector::classname($this->model);

        // get entity
        $class = $this->model;
        $entity = $id ? $this->syn->{$key}->find($id) : new $class();

        // no entity
        if(!$entity)
            $this->mog->oops($key . ' [' . $id . '] not found', 404);

        // processing
        $this->syn->{$key}->delete($entity);

        // go to list
        $this->mog->go('/' . $key);

        return [$key => $entity];
    }

}