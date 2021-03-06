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
namespace wicked\tools\meta;

/**
 * Singleton behavior
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2012-10-05
 * @version 1.0
 */
trait Singleton
{

    /** private constructor */
	protected function __construct() {}

    /**
     * Get instance
     * @static
     * @return Singleton
     */
    public static function instance()
	{
		static $instance;
		return empty($instance)
			? $instance = new self()
			: $instance;
	}

}