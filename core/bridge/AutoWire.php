<?php

namespace wicked\core\bridge;

use maestro\AutoWire;
use maestro\Registrar;

class ContextWire extends AutoWire
{

    public function __construct()
    {

        // add @context wire
        $this->add('context', function($object, $field, $data){
            $object->{$field} = Registrar::run($data);
        });

    }

}