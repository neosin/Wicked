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

use wicked\core\session\User;
use wicked\core\request\Globals;
use wicked\debug\Logger;

/**
 * God Controller
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2012-10-05
 * @version 1.0
 */
class Kernel extends Dispatcher
{

    /** @var \wicked\core\router\Route */
    public $route;

    /** @var \wicked\core\request\Globals */
    public $request;

    /** @var \wicked\core\session\User */
    public $session;

    /** @var \wicked\debug\Logger */
    public $logger;

    /** @var bool */
    protected $_running = false;


    /**
     * Constructor with user config
     */
    public function __construct(Router $router)
    {
        // init context
        $this->request = new Globals(true);
        $this->logger = new Logger();

        // set app token
        $this->token = 'wicked.app';

        // parent construct
        parent::__construct($router);

        // init session
        $this->session = new User($this->token);

        // ready !
        $this->logger->info('Hello :)');
    }


    /**
     * Run the whole web application
     * @param \wicked\core\Request $force
     * @throws \RuntimeException
     * @return mixed
     */
    public function run(Request $force = null)
    {
        // already running ?
        if($this->_running)
            throw new \RuntimeException('Application already running', 423);

        // start
        $this->_running = true;

        // generate request
        if($force)
            $this->request = $force;

        // route
        $this->route = $this->route($this->request);

        // build
        $output = $this->build($this->route);

        // thanks for playing :)
        $this->_running = false;

        return $output;
    }


    /**
     * Forward to another action
     * @param string $to
     * @param array $args
     * @return mixed
     */
    public function forward($to, array $args = [])
    {
        // new route
        $this->route->action = $to;
        $this->route->args = $args;

        // re-build
        return $this->build($this->route);
    }


    /**
     * Oops ! Something went wrong...
     * @param $message
     * @param $code
     */
    public function oops($message, $code = 418)
    {
//        $this->fire('oops', $this);
//        $this->fire($code, [$this, $message]);
    }


    /**
     * Badaboom !!
     */
    public function boom()
    {
        $this->oops('Boom !!');
    }

}