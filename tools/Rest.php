<?php

namespace wicked\services;

abstract class Rest
{

    /**
     * @static
     * @param $path
     * @param array $data
     * @param bool $slashed
     * @return string
     */
    public static function get($path, array $data = array(), $slashed = false)
	{
		if(!empty($data))
			$path .= (true === $slashed) ? '/'.implode('/', $data) : '?'.http_build_query($data);

		return self::query($path, 'GET');
	}

    /**
     * @static
     * @param $url
     * @param $data
     * @return string
     */
    public static function post($url, $data)
	{
		$header = 'Content-type: application/x-www-form-urlencoded';
		return self::query($url, 'POST', $header, $data);
	}

    /**
     * @static
     * @param $url
     * @param $method
     * @param null $header
     * @param null $data
     * @return string
     */
    public static function query($url, $method, $header = null, $data = null)
	{
		$config = array('http' => array('method' => $method));

		if($header)
			$config['http']['header'] = $header;

		if($data)
			$config['http']['content'] = $data;

		$context = stream_context_create($config);

		return file_get_contents($url, false, $context);
	}


}