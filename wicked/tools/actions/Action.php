<?php

namespace wicked\tools\actions;

use maestro\Registrar;

abstract class Action
{

    /** @var \wicked\core\Mog */
    protected $mog;

    /** @var \syn\core\ORM */
    protected $syn;

    /**
     * Get dependencies
     */
    public function __construct()
    {
        $this->mog = Registrar::run('wicked.mog');
        $this->syn = Registrar::run('wicked.syn');
    }

}