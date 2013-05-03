<?php

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