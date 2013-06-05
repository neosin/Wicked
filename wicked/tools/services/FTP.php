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
 * @date 2013-05-15
 * @version 1
 */
namespace wicked\tools\services;

/**
 * Class FTP
 * A simple FTP handler based on PHP5 native functions
 * @package wicked\libs
 */
class FTP
{

    /** @var resource */
    protected $_ftp;


    /**
     * Init FTP
     * @param $host
     * @param array $login
     * @throws \Exception
     */
    public function __construct($host, array $login = [])
    {
        // connect to host
        $this->_ftp = ftp_connect($host);

        // error
        if(!$this->_ftp)
            throw new \Exception('FTP : impossible to connect to [' . $host . '].');

        // login
        if($login) {
            list($username, $password) = $login;
            $logged = ftp_login($this->_ftp, $username, $password);

            // login fail
            if(!$logged)
                throw new \Exception('FTP : login failed.');
        }
    }


    /**
     * Upload a file
     * @param $localfile
     * @param $distantfile
     * @param int $mode
     * @return bool
     */
    public function put($localfile, $distantfile, $mode = FTP_ASCII)
    {
        $handle = fopen($localfile, 'r');
        $result = ftp_fput($this->_ftp, $distantfile, $handle, $mode);
        fclose($handle);

        return $result;
    }


    /**
     * Download a file
     * @param $distantfile
     * @param $localfile
     * @param int $mode
     * @return bool
     */
    public function get($distantfile, $localfile, $mode = FTP_ASCII)
    {
        $handle = fopen($localfile, 'w+');
        $result = ftp_fput($this->_ftp, $distantfile, $handle, $mode);
        fclose($handle);

        return $result;
    }


    /**
     * Create or Edit a dir
     * @param $dirname
     * @param string $chmod
     * @return string
     */
    public function dir($dirname, $chmod = null)
    {
        // create dir
        $dir = ftp_mkdir($this->_ftp, $dirname);

        // edit permissions
        $mod = $chmod ? ftp_chmod($this->_ftp, $chmod, $dirname) : true;

        return ($dir and $mod);
    }


    /**
     * Move into dir
     * @param $to
     * @return bool
     */
    public function move($to)
    {
        return ftp_chdir($this->_ftp, $to);
    }


    /**
     * Close FTP connection
     */
    public function __desctruct()
    {
        ftp_close($this->_ftp);
    }

}