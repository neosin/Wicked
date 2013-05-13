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
 * Class SQLite
 * @package syn
 */
class SQLite extends ORM
{

    /**
     * Init ORM for SQLite
     * @param string $dbfile
     */
    public function __construct($dbfile)
    {
        // create pdo for sqlite
        $pdo = new \PDO('sqlite:' . $dbfile);

        // construct
        parent::__construct($pdo);
    }

}