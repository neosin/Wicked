<?php

namespace wicked\wire;

use maestro\Registrar;

trait Model
{

    /**
     * Get globalized syn
     * @return mixed
     */
    protected function syn()
    {
        return Registrar::run('wicked.syn');
    }

}