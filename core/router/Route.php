<?php

namespace wicked\core\router;

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
