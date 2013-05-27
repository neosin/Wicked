<?php

/**
 * This file is part of the Syn package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @date 2013-04-24
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @version 0.8
 */
namespace syn\core;

/**
 * Class Collection
 * @package syn\core
 */
class Collection
{

    /** @var \syn\core\ORM */
    protected $_orm;

    /** @var string */
    protected $_table;

    /** @var string */
    protected $_model;

    /** @var array */
    protected $_filters = [];


    /**
     * Init a Collection binded to a specific table
     * @param ORM $orm
     * @param $table
     */
    public function __construct(ORM &$orm, $table)
    {
        $this->_orm = $orm;
        $this->_table = $table;
    }


    /**
     * Set class model
     * @param $model
     * @return $this
     */
    public function model($model = null)
    {
        // get
        if(!$model)
            return $this->_model;

        // set
        $this->_model = str_replace('/', '\\', $model);;
        return $this;
    }


    /**
     * Add entity filter
     * @param $hook
     * @param callable $callback
     * @return $this
     */
    public function filter($hook, callable $callback)
    {
        $this->_filters[$hook] = $callback;
        return $this;
    }


    /**
     * Apply filter
     * @param $filter
     * @param $entity
     * @return mixed
     */
    protected function apply($filter, $entity)
    {
        return empty($this->_filters[$filter])
            ? $entity
            : call_user_func($this->_filters[$filter], $entity);
    }


    /**
     * Find entity/entities
     * Ex: $syn->user->find();
     * Ex: $syn->user->find(['age' => 24]);
     * Ex: $syn->user->find(['age' => 24], 'name');
     * Ex: $syn->user->find(['age' => 24], ['name' => 'desc']);
     * @param array $where
     * @param array $orderBy
     * @return bool|\PDOStatement
     */
    public function find($where = [], $orderBy = [])
    {
        // prepare sql
        $sql = 'SELECT * FROM `' . $this->_orm->prefix() . $this->_table . '`';
        $multiple = true;

        // where clause
        if($where)
        {
            // multiple where
            if(is_array($where))
            {
                $sql .= ' WHERE 1';
                foreach($where as $field => $condition)
                    $sql .= ' AND `' . $field . '` = "' . $condition . '"';
            }
            // by identifier
            else
            {
                $multiple = false;
                $sql .= ' WHERE `id` = ' . $where;
            }
        }

        // order by clause
        if($orderBy) {

            $sql .= ' ORDER BY';

            // shortcut
            if(is_string($orderBy)) {
                $sql .= ' `' . $orderBy . '`';
            }
            else {
                foreach($orderBy as $field => $dir)
                    $sql .= ' `' . $field . '` ' . strtoupper($dir);
            }

        }

        // execute
        $sth = $this->_orm->pdo()->prepare($sql);
        $sth->execute();
        $result = $this->_model
            ? $sth->fetchAll(\PDO::FETCH_CLASS, $this->_model)
            : $sth->fetchAll(\PDO::FETCH_OBJ);

        return (!$multiple and count($result) > 0) ? $result[0] : $result;
    }


    /**
     * Find one by random
     * @param array $where
     * @return bool|mixed|\PDOStatement
     */
    public function random($where = [])
    {
        // get full list
        $data = $this->find($where);

        // get one by random
        return is_array($data)
            ? array_rand($data)
            : $data;
    }


    /**
     * Save entity
     * @param $entity
     * @return \PDOStatement
     */
    public function save(&$entity)
    {
        // cast to object
        $entity = (object)$entity;

        // filter before.save
        $filtered = $this->apply('before.save', $entity);

        // user cancel
        if($filtered === false)
            return false;

        // extract data
        $data = get_object_vars($entity);

        // insert
        if(empty($data['id']))
        {
            // exclude id
            unset($data['id']);

            // prepare sql
            $sql = 'INSERT INTO `' . $this->_orm->prefix() . $this->_table . '`';

            // fields
            $sql .= ' (`' . implode('`, `', array_keys($data)) . '`)';

            // values
            $sql .= ' VALUES ("' . implode('", "', $data) . '")';
        }
        // update
        else
        {
            // exclude id
            $id = $data['id'];
            unset($data['id']);

            // prepare sql
            $sql = 'UPDATE `' . $this->_orm->prefix() . $this->_table . '` SET ';

            // prepare set
            $set = [];
            foreach($data as $field => $value)
                $set[] = '`' . $field . '` = "' . $value . '"';

            // add values
            $sql .= implode(', ', $set);

            // where clause
            $sql .= ' WHERE `id` = ' . $id;
        }

        // execute
        $result = $this->_orm->pdo()->exec($sql);

        // re-hydrate object
        if($result) {

            // has id
            $newId = $entity->id ?: $this->_orm->pdo()->lastInsertId();
            if($newId)
                $entity = $this->find($newId);

        }

        // filter after.save
        $this->apply('after.save', $entity);

        return $result;
    }


    /**
     * Delete entity
     * @param $entity
     * @return bool|\PDOStatement
     */
    public function delete(&$entity)
    {
        // cast as object
        $entity = (object)$entity;

        // filter before.delete
        $filtered = $this->apply('before.save', $entity);

        // user cancel
        if($filtered === false)
            return false;

        // error
        if(empty($entity->id))
            return false;

        // prepare sql
        $sql = 'DELETE FROM `' . $this->_orm->prefix() . $this->_table . '`';

        // where clause
        $sql .= ' WHERE `id` = ' . $entity->id;

        // execute
        $result = $this->_orm->pdo()->exec($sql);

        // filter after.delete
        $this->apply('after.delete', $entity);

        return $result;
    }

}