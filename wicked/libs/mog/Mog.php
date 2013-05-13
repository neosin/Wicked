<?php

/**
 * This file is part of Mog.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-06
 * @version 1
 */
namespace mog;

/**
 * Class Mog
 * @package mog
 *
 * Extended request object with shortcuts and funny stuff
 */
class Mog extends Request implements \ArrayAccess
{

    /** @var string */
    public $ip;

    /** @var bool */
    public $local;

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

    /** @var array */
    protected $_bag = [];

    /** @var float */
    protected $_start;

    /** @var array */
    protected $_logs = [];


    /**
     * Create request from globals
     * @internal param bool $globals
     */
    public function __construct()
    {
        // env
        parent::__construct(true);

        // advanced data
        $this->ip = $this->server->remote_addr;
        $this->local = ($this->ip == '127.0.0.1' or $this->ip = '::1');
        $this->url = ((isset($this->server->https) and $this->server->https == 'on') ? 'https' : 'http') . '://' . $this->server->http_host . $this->server->request_uri;
        $this->lang = explode(',', $this->server->http_accept_language)[0];
        $this->method = $this->server->request_method;
        $this->async = isset($this->server->http_x_requested_with) and strtolower($this->server->http_x_requested_with) == 'xmlhttprequest';
        $this->sync = !$this->async;
        $this->mobile = isset($this->server->http_x_wap_profile) or isset($this->server->http_profile);

        // find browser
        foreach(['Firefox', 'Safari', 'Chrome', 'Opera', 'MSIE'] as $browser)
            if(strpos($this->server->http_user_agent, $browser))
                $this->browser = $browser;

        // stopwatch
        $this->_start = microtime(true);
    }


    /**
     * Get elasped time
     * @return mixed
     */
    public function elapsed()
    {
        $elapsed = microtime(true) - $this->_start;
        return number_format($elapsed, 4);
    }


    /**
     * Redirect to url
     * @param string $url
     * @param int $code
     */
    public function go($url, $code = 200)
    {
        header('Location: ' . $url, true, $code);
        exit;
    }


    /**
     * Upload a file to a specific dir
     * @param $filename
     * @param $to
     * @param null $name
     * @return bool
     */
    public function upload($filename, $to, $name = null)
    {
        if(isset($this->files[$filename]))
            $this->oops('No file named "' . $filename . '" to upload !');

        $tmp = $this->files[$filename]['tmp_name'];
        $name = $name ?: $this->files[$filename]['name'];
        return move_uploaded_file($tmp, rtrim($to, '/') . '/' . $name);
    }


    /**
     * Download a file
     * @param $file
     */
    public function download($file)
    {
        $basename = basename($file);
        header('Content-Type: application/force-download; name="' . $basename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize('./' . $basename));
        header('Content-Disposition: attachment; filename="' . $basename . '"');
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile('./'. $basename);
        exit;
    }


    /**
     * Output data as JSon
     * @param array $data
     */
    public function json(array $data)
    {
        header('Content-type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        die(json_encode($data));
    }


    /**
     * Read content of file or distant resource
     * @param $target
     * @return bool|string
     */
    public function read($target)
    {
        // try get_contents
        $content = @file_get_contents($target);

        // works ?
        if($content !== false)
            return $content;

        // try curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $content = curl_exec($ch);
        curl_close($ch);

        // works ?
        if($content !== false)
            return $content;

        return false;
    }


    /**
     * Write in a file
     * @param $content
     * @param $in
     * @return int
     */
    public function write($content, $in)
    {
        return file_put_contents($in, $content);
    }


    /**
     * Give some help to debug stuff
     */
    public function debug()
    {
        die(call_user_func_array('var_dump', func_get_args()));
    }


    /**
     * Huum, something went wrong here...
     * @param $message
     * @param int $code
     * @throws MogException
     */
    public function oops($message, $code = 0)
    {
        throw new MogException('Kupo ! ' . $message, $code);
    }


    /**
     * Get item in bag
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->_bag[$key] = $value;
    }


    /**
     * Store item in bag
     * @param mixed $key
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if(!$this->offsetExists($key))
            $this->oops('You did not put any "' . $key . '" in your bag !');

        return $this->_bag[$key];
    }


    /**
     * Check if an item exists in bag
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->_bag);
    }


    /**
     * Remove item in bag
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->_bag[$key]);
    }


    /**
     * Alias of offsetExists
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }


    /**
     * Alias of offsetUnset
     * @param $key
     */
    public function drop($key)
    {
        $this->offsetUnset($key);
    }


    /**
     * Get back the whole bag
     * @return array
     */
    public function bag()
    {
        return $this->_bag;
    }


    /**
     * Add log
     * @param $message
     * @param int $level
     */
    public function log($message, $level = 0)
    {
        $this->_logs[] = (object)[
            'message' => $message,
            'level' => $level,
            'time' => $this->elapsed()
        ];
    }


    /**
     * Get all logs
     * @return array
     */
    public function logs()
    {
        return $this->_logs;
    }


    /**
     * Easter Egg, Kupo !
     * @return string
     */
    public function __toString()
    {
        $dialog = [
            'Kupo ?!',
            'I\'m hungry...',
            'May I help you ?',
            'It\'s dark in here...',
            'I haven\'t received any mail lately, Kupo.',
            'It\'s dangerous outside ! Kupo !',
            'Don\'t call me if you don\'t need me, Kupo !',
            'What do you want to do, Kupo ?'
        ];

        return 'o-&#949;(:o) ' . $dialog[array_rand($dialog)];
    }

}