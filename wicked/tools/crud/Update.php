<?php

namespace wicked\tools\crud;

trait Update
{

    use \wicked\wire\All;

    /** @var string */
    public $model;

    /**
     * Update entity
     * @param $id
     * @return array
     */
    public function update($id)
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