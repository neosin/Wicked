<?php

namespace wicked\tools;

abstract class String
{

    /**
     * Truncate a string
     * @param $string
     * @param $length
     * @return string
     */
    public static function truncate($string, $length)
    {
        return substr($string, 0, $length);
    }


    /**
     * Remove a specified segment in string
     * @param $string
     * @param $segment
     * @return mixed
     */
    public static function remove($string, $segment)
    {
        return str_replace($segment, '', $string);
    }


    /**
     * Check if segment exists in string
     * @param $string
     * @param $segment
     * @return bool
     */
    public static function has($string, $segment)
    {
        return (bool)strpos($string, $segment);
    }


    /**
     * Extract placeholders from string using regex
     * @param $string
     * @param $pattern
     * @return array|bool
     */
    public static function extract($string, $pattern)
    {
        $success = preg_match($pattern, $string, $out);
        return $success ? $out : (bool)$success;
    }


    /**
     * Create hash from string
     * @param $string
     * @param string $salt
     * @return string
     */
    public static function hash($string, $salt = '')
    {
        return sha1(uniqid($salt).md5($string));
    }

}