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

class AutoWire
{

    /** @var array */
    protected $_keywords = [];


    /**
     * Add a parser callback
     * @param $keyword
     * @param callable $callback
     */
    public function add($keyword, callable $callback)
    {
        $this->_keywords[$keyword] = $callback;
    }


    /**
     * Apply binding to object
     * @param $object
     */
    public function apply(&$object)
    {
        // get reflection on objet
        $ref = new \ReflectionObject($object);

        // for each properties
        foreach($ref->getProperties() as $property)
        {
            // get infos
            $field = $property->getName();
            $doc = $property->getDocComment();

            // parse doc
            foreach($this->_keywords as $keyword => $callback)
            {
                // if keyword found : apply callback
                if(preg_match('/@' . $keyword . ' ([a-zA-Z0-9._\- ]+)/', $doc, $out))
                    call_user_func_array($callback, [$object, $field, $out[1]]);
            }
        }
    }

}