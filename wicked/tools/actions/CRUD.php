<?php

namespace wicked\tools\actions;

class CRUD extends Action
{

    /** @var string */
    protected $key;

    /** @var string */
    protected $model;


    /**
     * Get dependencies
     */
    public function __construct($key, $model)
    {
        $this->key = $key;
        $this->model = $model;
        parent::__construct();
    }


    /**
     * Create entity
     * @param $data
     * @return array
     */
    protected function create($data)
    {
        // create entity
        $class= $this->model;
        $entity = new $class();

        // hydrate and save object
        $this->mog->hydrate($entity, $data);
        $success = $this->syn->{$this->key}->save($entity);

        return $success ? $entity : false;
    }


    /**
     * Get one or all entities
     * @param null $id
     * @return bool
     */
    protected function read($id = null)
    {
        // listing
        if(is_null($id))
            return $this->syn->{$this->key}->find();

        // get entity
        if($entity = $this->syn->{$this->key}->find($id))
            return $entity;

        // entity not found
        return false;
    }


    /**
     * Update entity
     * @param $id
     * @param $data
     * @return bool
     */
    protected function update($id, $data)
    {
        // get entity
        $entity = $this->syn->{$this->key}->find($id);

        // not exist
        if(!$entity)
            return false;

        // hydrate and save object
        $this->mog->hydrate($entity, $data);
        $success = $this->syn->{$this->key}->save($entity);

        return $success ? $entity : false;
    }


    /**
     * Delete entity
     * @param $id
     * @return bool
     */
    protected function delete($id)
    {
        // get entity
        $entity = $this->syn->{$this->key}->find($id);

        // not exist
        if(!$entity)
            return false;

        // delete object
        $success = $this->syn->{$this->key}->delete($entity);

        return $success;
    }

}