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

class View
{

    /** @var string */
    protected $_file;

    /** @var View */
    protected $_layout;

    /** @var array */
    protected $_args = [];

    /** @var array */
    protected $_slots = [];


    /**
     * @param $file
     * @param array $args
     * @throws \InvalidArgumentException
     */
    public function __construct($file, array $args = [])
    {
        $file = strtolower($file);

        if(!file_exists($file))
            throw new \InvalidArgumentException('Template [' . $file . '] does not exist', 404);

        $this->_file = $file;
        $this->_args = $args;
    }


    /**
     * Assign var
     * @param $name
     * @param null $value
     * @return $this
     */
    public function set($name, $value)
    {
        if(is_array($name)) {
            foreach($name as $key => $value)
                $this->_args[$key] = $value;
        }
        else
            $this->_args[$name] = $value;

        return $this;
    }


    /**
     * Add content to slot
     * @param $name
     * @param $content
     * @return $this
     */
    public function slot($name, $content)
    {
        $this->_slots[$name] = $content;
        return $this;
    }


    /**
     * Generate template
     * @return string
     */
    public function display()
    {
        // compile
        $content = $this->compile();

        // has layout ?
        if($this->_layout)
        {
            // give slots
            foreach($this->_slots as $slot => $value)
                $this->_layout->slot($slot, $value);

            // give args
            foreach($this->_args as $name => $arg)
                $this->_layout->set($name, $arg);

            // push content
            $this->_layout->slot('content', $content);
            return $this->_layout->display();
        }
        else
            return $content;
    }

    /**
     * Compile and return content
     * @return string
     */
    protected function compile()
    {
        // start streaming
        ob_start();

        // extract args
        extract($this->_args);

        // import view
        require $this->_file;

        // get content
        return ob_get_clean();
    }


    /**
     * Inner access : Set layout
     * @param $file
     * @param array $args
     */
    protected function layout($file, array $args = [])
    {
        $this->_layout = new self($file, $args);
    }


    /**
     * Inner access : Import raw partial
     * @param $file
     * @throws \InvalidArgumentException
     */
    protected function partial($file)
    {
        if(!file_exists($file))
            throw new \InvalidArgumentException('Partial [' . $file . '] does not exist in ' . $this->_file);

        require $file;
    }


    /**
     * Inner access : Display slot
     * @param $name
     * @return mixed
     */
    protected function hook($name)
    {
        return empty($this->_slots[$name])
            ? null
            : $this->_slots[$name];
    }


    /**
     * Inner access : Shortcut content slot
     * @return string
     */
    protected function content()
    {
        return $this->hook('content');
    }


    /**
     * Shortcut : display
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }


    /**
     * Meta markup
     * @return string
     */
    protected function meta()
    {
        return '
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1" />
        ';
    }


    /**
     * CSS markup
     * @return string
     */
    protected function css()
    {
        $str = '';
        foreach(func_get_args() as $file)
            $str .= '<link type="text/css" media="screen" href="' . static::asset('css/' . $file . '.css') . '" rel="stylesheet" />';

        return $str;
    }


    /**
     * JS markup
     * @return string
     */
    protected function js()
    {
        $str = '';
        foreach(func_get_args() as $file)
            $str .= '<script type="text/javascript" src="' . static::asset('js/' . $file . '.js') . '"></script>';

        return $str;
    }

    /**
     * Asset public file
     * @param $filename
     * @return string
     */
    protected function asset($filename)
    {
        return url('') . 'public/' . $filename;
    }

}
