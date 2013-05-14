<?php

/**
 * This file is part of the Wicked package.
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
namespace wicked\core;

use maestro\AutoWire;
use maestro\Registrar;

class Wire extends AutoWire
{

    public function __construct()
    {

        // add @context wire
        $this->add('context', function($object, $field, $data){
            $object->{$field} = Registrar::run($data);
        });

    }

}