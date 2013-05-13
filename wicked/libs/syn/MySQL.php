<?php

/**
 * This file is part of the Syn package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @date 2013-04-24
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @version 0.8
 */
namespace syn;

use syn\core\ORM;

/**
 * Class MySQL
 * @package syn
 */
class MySQL extends ORM
{

    /**
     * Init ORM for MySQL
     * @param string $dbname
     * @param array $config
     */
    public function __construct($dbname, array $config = [])
    {
        // default config
        list($host, $username, $password, $prefix) = $config + ['127.0.0.1', 'root', '', ''];

        // create pdo for mysql
        $pdo = new \PDO('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password);

        // construct
        parent::__construct($pdo, $prefix);
    }

}
