<?php

namespace wicked\tools\crud;

trait Create
{

    use \wicked\wire\All;

    /** @var string */
    public $model;

    /**
     * Add entity
     * @return array
     */
    public function create()
    {
        // parse
        $key = \wicked\tools\Inflector::classname($this->model);

        // get entity
        $class = $this->model;
        $entity = new $class();

        // processing
        if($post = $this->mog->post) {

            // hydrate and save object
            $this->mog->hydrate($entity, $post);
            $this->syn->{$key}->save($entity);

            // go to read page
            $this->mog->go('/' . $key . '/read/' . $entity->id);

        }

        return [$key => $entity];
    }

}