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

abstract class Inflector
{

    /**
     * CamelCase to flat_case
     * @param string $str
     * @return string
     */
    public static function flatten($str)
    {
        $str = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $str);
        return strtolower($str);
    }


    /**
     * flat_case to CamelCase or camelBackCase
     * @param string $str
     * @param bool $camelBack
     * @return string
     */
    public static function camelize($str, $camelBack = false)
    {
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = (true === $camelBack) ? lcfirst($str) : $str;
        return str_replace(' ', '', $str);
    }


    /**
     * get classname from object or string
     * @param mixed $object
     * @return string
     */
    public static function classname($object)
    {
        $class = is_object($object) ? get_class($object) : $object;
        $splitted = explode('\\', $class);
        return end($splitted);
    }

}
