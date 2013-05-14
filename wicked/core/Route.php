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

class Route
{

    /** @var string */
    public $pattern;

    /** @var string */
    public $action;

    /** @var array */
    public $args = [];

    /** @var string */
    public $view;

    /** @var array */
    public $defaults = [];

    /** @var callable */
    public $filter;


    /**
     * Create a new route
     * @param $pattern
     * @param $action
     * @param null $view
     * @param array $defaults
     * @param callable $filter
     */
    public function __construct($pattern, $action, $view = null, $defaults = [], callable $filter = null)
    {
        $this->pattern = $pattern;
        $this->action = $action;
        $this->view = $view;
        $this->defaults = $defaults;
        $this->filter = $filter;
    }

}
