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
namespace wicked\core\request;

use wicked\core\Request;

class Globals extends Request
{

    /** @var string */
    public $ip;

    /** @var string */
    public $url;

    /** @var string */
    public $lang = 'fr-FR';

    /** @var string */
    public $method = 'get';

    /** @var bool */
    public $sync = true;

    /** @var bool */
    public $async = false;

    /** @var bool */
    public $mobile = false;

    /** @var string */
    public $browser = 'unknown';

    /** @var int */
    protected $_start;


    /**
     * Create request from globals
     * @param bool $globals
     */
    public function __construct($globals = false)
    {
        if($globals) {

            // env
            parent::__construct(true);

            // advanced data
            $this->ip = $this->server->remote_addr;
            $this->url = 'http://' . $this->server->http_host . $this->server->request_uri;
            $this->lang = explode(',', $this->server->http_accept_language)[0];
            $this->method = $this->server->request_method;
            $this->async = isset($this->server->http_x_requested_with) and strtolower($this->server->http_x_requested_with) == 'xmlhttprequest';
            $this->sync = !$this->async;
            $this->mobile = isset($this->server->http_x_wap_profile) or isset($this->server->http_profile);

            // find browser
            foreach(['Firefox', 'Safari', 'Chrome', 'Opera', 'MSIE'] as $browser)
                if(strpos($this->server->http_user_agent, $browser))
                    $this->browser = $browser;

        }

        // stopwatch
        $this->_start = microtime(true);
    }


    /**
     * Get elasped time
     * @return mixed
     */
    public function elasped()
    {
        return microtime(true) - $this->_start;
    }

}