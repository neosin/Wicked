<?php

/**
 * This file is part of the Maestro package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-02
 * @version 0.1
 */
namespace maestro;

class Wrapper
{

    /** @var string */
    protected $class;

    /** @var array */
    protected $args = [];

    /** @var array */
    protected $properties = [];


    /**
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }


    /**
     * Set constructor args
     * @param array $args
     */
    public function args(array $args = [])
    {
        $this->args = $args;
    }


    /**
     * Set constructor properties
     * @param array $properties
     */
    public function properties(array $properties = [])
    {
        $this->properties = $properties;
    }


    /**
     * Set constructor property
     * @param $property
     * @param $value
     */
    public function property($property, $value)
    {
        $this->args[$property] = $value;
    }


    /**
     * Run a new instance
     * @return object
     */
    public function run()
    {
        // init args
        $args = [];

        // find inner-dependencies
        foreach($this->args as $arg)
            $args[] = $this->resolve($arg);

        // create object
        $reflection = new \ReflectionClass($this->class);
        $object =  $reflection->newInstanceArgs($args);

        // add properties
        foreach($this->properties as $property => $value)
            $object->{$property} = $this->resolve($value);

        return $object;
    }


    /**
     * Resolve string-based dependecy for args or properties
     * @param $arg
     * @return string
     */
    protected function resolve($arg)
    {
        if(is_string($arg) and substr($arg, 0, 1) == '@')
        {
            $ref = substr($arg, 1);
            return Registrar::run($ref);
        }

        return $arg;
    }

}