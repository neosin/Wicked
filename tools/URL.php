<?php

namespace wicked\libs;

class URL
{

    /** @var string */
    public $full = '';

    /** @var string */
    public $scheme = '';

    /** @var string */
    public $host = '';

    /** @var int */
    public $port = 0;

    /** @var string */
    public $user = '';

    /** @var string */
    public $pass = '';

    /** @var string */
    public $path = '';

    /** @var array */
    public $segments = [];

    /** @var string */
    public $query = '';

    /** @var array */
    public $args = [];

    /** @var string */
    public $fragment = '';


    /**
     * Create url
     * @param string $url
     * @throws \InvalidArgumentException
     */
    public function __construct($url = null)
    {
        // input url
        if($url)
        {
            // store full url
            $this->full = $url;

            // try parsing
            $data = parse_url($url);

            // fail
            if(!$data)
                throw new \InvalidArgumentException;

            // hydrate data
            foreach($data as $name => $value)
                $this->{$name} = $value;

            // parse path
            $this->segments = explode('/', $this->path);

            // parse query
            $couples = explode('&', $this->query);
            foreach($couples as $couple)
            {
                $exp = explode('=', $couple);
                $this->args[$exp[0]] = $exp[1];
            }
        }
    }


    /**
     * Build url from properties
     * @return string
     */
    public function build()
    {
        // build path
        $this->path = implode('/', $this->segments);

        // build query
        $imp = [];
        foreach($this->args as $name => $value)
            $imp = $name .'=' . $value;

        $this->query = implode('&', $imp);

        // build url
        $this->full = http_build_url((array)$this);

        return $this->full;
    }


    /**
     * Return URL as string
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }


    /**
     * Get current url
     * @return URL
     */
    public static function current()
    {
        // get protocol
        $url = ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $url .= '://';

        if($_SERVER['SERVER_PORT'] != '80')
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        else
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        return new self($url);
    }

}
