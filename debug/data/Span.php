<?php

namespace wicked\debug\data;

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