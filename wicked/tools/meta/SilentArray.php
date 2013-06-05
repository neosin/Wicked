<?php

namespace wicked\tools\meta;

class SilentArray extends \ArrayObject
{

    /**
     * Override : return null if not exists
     * @param mixed $index
     * @return mixed|void
     */
    public function offsetGet($index)
    {
        return $this->offsetExists($index) ? $this[$index] : null;
    }

}