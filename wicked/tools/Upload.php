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
namespace wicked\tools;

class Upload
{

    /** @var string */
    public $tmp;

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var array */
    public $error;

    /** @var int */
    public $size;

    /** @var array */
    protected static $formats = [];


    /**
     * Create upload handle
     * @param $key
     */
    public function __construct($key)
    {
        // get tmp file
        $handle = $_FILES[$key];

        // hydrate
        $this->tmp = $handle['tmp_name'];
        $this->name = $handle['name'];
        $this->type = $handle['type'];
        $this->error = $handle['error'];
        $this->size = $handle['size'];
    }


    /**
     * Valid file to format
     * @param $format
     * @return bool
     */
    public function valid($format)
    {
        // init
        $format = static::$formats[$format];
        $valid = true;

        // min size
        if($format->minsize and $this->size < $format->minsize)
            $valid = false;

        // max size
        if($format->maxsize and $this->size > $format->maxsize)
            $valid = false;

        // extensions
        if($format->extensions and strpos($format->extensions, $this->name))
            $valid = false;

        return $valid;
    }


    /**
     * Move file to final destination
     * @param $path
     * @param null $filename
     * @return bool
     */
    public function save($path, $filename = null)
    {
        $filename = $filename ?: $this->name;
        return move_uploaded_file($this->tmp, $path . DIRECTORY_SEPARATOR . $filename);
    }


    /**
     * Shortcut
     * @param $file
     * @param $path
     * @param null $filename
     * @return bool
     */
    public static function to($file, $path, $filename = null)
    {
        $upload = new self($file);
        return $upload->save($path, $filename);
    }

}