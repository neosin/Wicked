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

use wicked\core\Events;

/**
 * God Controller
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2012-10-05
 * @version 1.0
 */
class Kernel extends Dispatcher
{

    use Events;

    /** @var \wicked\core\Mog */
    public $mog;

    /** @var bool */
    protected $_running = false;


    /**
     * Constructor with user config
     */
    public function __construct(Router $router)
    {
        // init context
        $this->mog = new Mog();

        // parent construct
        parent::__construct($router);

        // ready !
        $this->mog->log->info('Hello :)');
    }


    /**
     * Run the whole web application
     * @param \wicked\core\Mog $force
     * @throws \RuntimeException
     * @return mixed
     */
    public function run(Mog $force = null)
    {
        // already running ?
        if($this->_running)
            throw new \RuntimeException('Application already running', 423);

        // start
        $this->_running = true;

        // generate request
        if($force)
            $this->mog = $force;

        // route
        $route = $this->route($this->mog);
        $this->mog->route = &$route;

        // build
        $output = $this->build($this->mog->route);

        // thanks for playing :)
        $this->_running = false;

        return $output;
    }

}