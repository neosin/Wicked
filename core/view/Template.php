<?php

namespace wicked\core\view;

use wicked\core\Loader;

class Template
{

    /** @var string */
    protected $file;

    /** @var Template */
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
        if(!file_exists($file))
            throw new \InvalidArgumentException('Template [' . $file . '] does not exist');

        $this->file = $file;
        $this->args = $args;
        $this->helper('wicked/core/view/Html');
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
     * Add helper to the view
     * @param $class
     */
    public function helper($class)
    {
        $this->helpers[] = $class;
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

            // give helper
            foreach($this->helpers as $helper)
                $this->layout->helper($helper);

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
     * Shortcut : display
     * @param $file
     * @param array $args
     */
    public static function forge($file, array $args = [])
    {
        $template = new self($file, $args);
        $template->display();
    }

}
