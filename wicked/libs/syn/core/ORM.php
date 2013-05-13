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
 * Class ORM
 * @package syn\core
 */
class ORM
{

    /** @var \PDO */
    protected $_pdo;

    /** @var array */
    protected $_collections = [];

    /** @var  string */
    protected $_prefix;


    /**
     * Init ORM with PDO
     * @param \PDO $pdo
     * @param string $prefix
     */
    public function __construct(\PDO $pdo, $prefix = null)
    {
        $this->_pdo = $pdo;
        $this->_prefix = $prefix;
    }


    /**
     * Sync model with database
     * @return \PDOStatement
     */
    public function synchronize()
    {
        // init schema
        $tables = [];

        /**
         * get schema for each models
         * @var $collection Collection
         */
        foreach($this->_collections as $name => $collection) {

            // init infos
            $model = $collection->model();
            $table = [];

            // get ref
            $ref = new \ReflectionClass($model);

            // get parameters name and type
            foreach($ref->getProperties() as $property) {

                // get phpdoc
                $doc = $property->getDocComment();

                // get type
                if($property->isPublic() and preg_match('/@var ([a-z ]+)/', $doc, $matches))
                    $table[$property->getName()] = trim($matches[1]);
            }

            // add table to schema
            $tables[$name] = $table;

        }

        // create tables
        $query = [];

        foreach($tables as $name => $details) {

            // create table if not exists
            $subquery = 'create table if not exists `' . $this->_prefix . $name . '` (';

            foreach($details as $field => $prop) {
                $subquery .= '`' . $field . '` ';

                // define type
                switch($prop) {
                    case 'int' : $type = 'int'; break;
                    case 'string' : $type = 'varchar(255)'; break;
                    case 'string text' : $type = 'text'; break;
                    case 'string date' : $type = 'datetime';  break;
                    default: $type = 'varchar(255)'; break;
                }

                $subquery .= $type . ' ';

                // id ?
                $subquery .= ($field == 'id') ? ' not null auto_increment,' : ' default null,';

            }

            $subquery .= 'primary key (`id`));';

            // add to general query
            $query[] = $subquery;

        }

        // alter fields
        foreach($tables as $name => $details) {

            // alter table
            $subquery = 'alter table `' . $this->_prefix . $name . '` ';

            foreach($details as $field => $prop) {
                $subquery .= 'modify `' . $field . '` ';

                // define type
                switch($prop) {
                    case 'int' : $type = 'int'; break;
                    case 'string' : $type = 'varchar(255)'; break;
                    case 'string text' : $type = 'text'; break;
                    case 'string date' : $type = 'datetime';  break;
                    default: $type = 'varchar(255)'; break;
                }

                $subquery .= $type . ' ';

                // id ?
                $subquery .= ($field == 'id') ? ' not null auto_increment,' : ' default null,';

            }

            // add to general query
            $query[] = $subquery;
        }

        return $this->query(implode("\n", $query));
    }


    /**
     * Get current PDO
     * @return \PDO
     */
    public function pdo()
    {
        return $this->_pdo;
    }


    /**
     * Get table prefix
     * @return null|string
     */
    public function prefix()
    {
        return $this->_prefix;
    }


    /**
     * Execute SQL query
     * @param $sql
     * @param null $cast
     * @return \PDOStatement
     */
    public function query($sql, $cast = null)
    {
        // execute
        $result = $cast
            ? $this->_pdo->query($sql, \PDO::FETCH_CLASS, $this->_collections[$cast]->model())
            : $this->_pdo->query($sql, \PDO::FETCH_OBJ);

        return $result->fetchAll();
    }


    /**
     * Create or retrieve a Collection statement
     * @param $entity
     * @return Collection
     */
    public function __get($entity)
    {
        // if not exists yet
        if(empty($this->_collections[$entity])) {
            $this->_collections[$entity] = new Collection($this, $entity);
        }

        // get collection
        $collection = &$this->_collections[$entity];

        return $collection;
    }


    /**
     * Create a backup of the database in sql
     * @param $filename string
     * @return string
     */
    public function backup($filename)
    {

        // get table list
        $tables = [];
        foreach($this->_pdo->query('show tables')->fetchAll() as $row)
            $tables[] = $row[0];

        // init backup sql
        $backup = '';

        // table structure
        foreach($tables as $table) {

            // create table
            $backup .= 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n";
            $backup .= 'CREATE TABLE `' . $table . '` (' . "\n";

            // get fields
            $fields = $this->_pdo->query('describe `' . $table . '`')->fetchAll();

            // build lines
            $lines = [];
            foreach($fields as $field) {

                // new line
                $line = "\t";

                // name and type
                $line .= '`' . $field['Field'] . '` ' . $field['Type'];

                // default value
                if($field['Default'])
                    $line .= ' DEFAULT ' . $field['Default'];

                // null value
                if($field['Null'] == 'NO')
                    $line .= ' NOT NULL';

                // extra value
                if($field['Extra'])
                    $line .= ' ' . $field['Extra'];

                // primary key
                if($field['Key'] == 'PRI')
                    $line .= ' PRIMARY KEY';

                // push line
                $lines[] = $line;
            }

            $backup .= implode($lines, ",\n") . "\n);\n\n";
        }

        // data backup
        $pdo = $this->_pdo;
        foreach($tables as $table) {

            // find data
            $lines = [];
            foreach($this->__get($table)->find() as $row) {

                // new line
                $line = 'INSERT INTO `' . $table . '` (`';

                // add fields
                $fields = array_keys(get_object_vars($row));
                $line .= implode($fields, '`, `');

                // add values
                $line .= '`) VALUES (';
                $values = get_object_vars($row);

                // escape input
                $values = array_map(function($a) use(&$pdo) { return $pdo->quote($a);  }, $values);
                $line .= implode($values, ', ');

                // close and push line
                $line .= ');';
                $lines[] = $line;

            }

            $backup .= implode($lines, "\n") . "\n\n";

        }

        // write backup in file
        return file_put_contents($filename, $backup);
    }

}
