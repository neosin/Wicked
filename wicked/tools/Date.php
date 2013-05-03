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

abstract class Date
{

    /** @var array */
    protected static $formats = [
        'date' => 'Y-m-d',
        'time' => 'H:i:s',
        'full' => 'Y-m-d H:i:s'
    ];

    /** @var array */
    protected static $langs = [
        'en' => [
            'months' => ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
            'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
        ],
        'fr' => [
            'months' => ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
            'days' => ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
        ]
    ];


    /**
     * Set a format
     * @param $name
     * @param $set
     */
    public static function set($name, $set)
    {
        static::$formats[$name] = $set;
    }


    /**
     * Get formatted date
     * @param $format
     * @param null $time
     * @return string
     */
    public static function get($format, $time = null)
    {
        return date(static::$formats[$format], $time);
    }


    /**
     * Set language details
     * @param $name
     * @param array $months
     * @param array $days
     */
    public static function lang($name, array $months, array $days)
    {
        static::$langs[$name] = compact('months', 'days');
    }


    /**
     * Return year of specified date
     * @param $date
     * @return string
     */
    public static function yearOf($date)
    {
        $date = static::convert($date);
        return date('Y', $date);
    }


    /**
     * Get current year
     * @return string
     */
    public static function year()
    {
        return date('Y');
    }


    /**
     * Get specified month
     * @param $date
     * @param null $lang
     * @return string
     */
    public static function monthOf($date, $lang = null)
    {
        $date = static::convert($date);
        return $lang
            ? static::$langs[$lang]['months'][date('n', $date) - 1]
            : date('m', $date);
    }


    /**
     * Get current month
     * @param null $lang
     * @return string
     */
    public static function month($lang = null)
    {
        return static::monthOf(time(), $lang);
    }


    /**
     * Get specified day
     * @param $date
     * @param null $lang
     * @return string
     */
    public static function dayOf($date, $lang = null)
    {
        $date = static::convert($date);
        return $lang
            ? static::$langs[$lang]['days'][date('w', $date)]
            : date('d', $date);
    }


    /**
     * Get current day
     * @param null $lang
     * @return string
     */
    public static function day($lang = null)
    {
        return static::dayOf(time(), $lang);
    }


    /**
     * Get specified hour
     * @param $date
     * @return string
     */
    public static function hourOf($date)
    {
        $date = static::convert($date);
        return date('H', $date);
    }


    /**
     * Get current hour
     * @return string
     */
    public static function hour()
    {
        return date('H');
    }


    /**
     * Get specified minute
     * @param $date
     * @return string
     */
    public static function minuteOf($date)
    {
        $date = static::convert($date);
        return date('i', $date);
    }


    /**
     * Get current minute
     * @return string
     */
    public static function minute()
    {
        return date('i');
    }


    /**
     * Get specified second
     * @param $date
     * @return string
     */
    public static function secondOf($date)
    {
        $date = static::convert($date);
        return date('s', $date);
    }


    /**
     * Get current second
     * @return string
     */
    public static function second()
    {
        return date('s');
    }


    /**
     * Get current millisecond
     * @return mixed
     */
    public static function ms()
    {
        return microtime(true);
    }


    /**
     * Get current date
     * @return string
     */
    public static function date($of = null)
    {
        $of = $of ? static::convert($of) : time();
        return date(static::get('partial'), $of);
    }


    /**
     * Get current time
     * @return string
     */
    public static function time($of = null)
    {
        $of = $of ? static::convert($of) : time();
        return date(static::get('time'), $of);
    }


    /**
     * Get full datetime
     * @return string
     */
    public static function full($of = null)
    {
        $of = $of ? static::convert($of) : time();
        return date(static::get('full'), $of);
    }


    /**
     * Convert string-date to timestamp
     * @param $date
     * @return int
     */
    public static function convert($date)
    {
        return is_int($date) ? $date : strtotime($date);
    }


    /**
     * Execute an operation on a date
     * @param $date
     * @param $operation
     * @return int
     */
    public static function apply($date, $operation)
    {
        $date = static::convert($date);
        return strtotime($operation, $date);
    }

}