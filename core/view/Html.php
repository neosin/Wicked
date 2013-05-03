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
namespace wicked\core\view;

abstract class Html
{

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