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
namespace wicked\dev\data;

class Span
{

    /** @var string */
    public $name;

    /** @var float */
    public $start;

    /** @var float */
    public $end;

    /**
     * Get duration
     * @return float
     */
    public function duration()
    {
        return $this->end - $this->start;
    }

}