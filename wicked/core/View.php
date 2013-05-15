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
    protected $file;

    /** @var View */
    protected $layout;

    /** @var array */
    protected $args = [];

    /** @var array */
    protected $slots = [];

    /** @var array */
    protected $helpers = [];


    /**
     * @param $file
     * @param array $args
     * @throws \InvalidArgumentException
     */
    public function __construct($file, array $args = [])
    {
        $file = strtolower($file);

        if(!file_exists($file))
            throw new \InvalidArgumentException('Template [' . $file . '] does not exist');

        $this->file = $file;
        $this->args = $args;
    }


    /**
     * Add content to slot
     * @param $name
     * @param $content
     * @return $this
     */
    public function slot($name, $content)
    {
        $this->slots[$name] = $content;
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
        if($this->layout)
        {
            // give slots
            foreach($this->slots as $slot => $value)
                $this->layout->slot($slot, $value);

            // give args ? @todo

            // push content
            $this->layout->slot('content', $content);
            return $this->layout->display();
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
        extract($this->args);

        // inject helpers
        foreach($this->helpers as $helper)
            Loader::load($helper);

        // import view
        require $this->file;

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
        $this->layout = new self($file, $args);
    }


    /**
     * Inner access : Import raw partial
     * @param $file
     * @throws \InvalidArgumentException
     */
    protected function partial($file)
    {
        if(!file_exists($file))
            throw new \InvalidArgumentException('Partial [' . $file . '] does not exist in ' . $this->file);

        require $file;
    }


    /**
     * Inner access : Display slot
     * @param $name
     * @return mixed
     */
    protected function hook($name)
    {
        return empty($this->slots[$name])
            ? null
            : $this->slots[$name];
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
    protected static function meta()
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
    protected static function css()
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
    protected static function js()
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
    protected static function asset($filename)
    {
        return url('') . 'public/' . $filename;
    }

}
