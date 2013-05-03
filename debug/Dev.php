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
namespace wicked\debug;

abstract class Dev
{

    /** @var array */
    protected static $_ips = ['127.0.0.1', '::1'];

    /** @var bool */
    protected static $_enabled = true;


    /**
     * Add ip for dev mode
     * @param $ips
     */
    public static function allow($ips)
    {
        // force array
        $ips = (array)$ips;

        // add to ips list
        foreach($ips as $ip)
            static::$_ips[] = $ip;
    }


    /**
     * Turn off dev mode
     */
    public static function off()
    {
        static::$_enabled = false;
    }


    /**
     * Check if dev mode and allowed ip
     */
    public static function mode()
    {
        return (static::$_enabled and in_array($_SERVER['REMOTE_ADDR'], static::$_ips));
    }

}