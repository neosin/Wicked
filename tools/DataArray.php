<?php

namespace wicked\tools;

abstract class DataArray extends \ArrayObject
{

    /** @var array */
    protected $data = [];

    /**
     * Get current index
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set current index
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Check if current index exists
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Unset current index
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get back to the first index
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Get current key
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Next index
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Index exists
     * @return bool
     */
    public function valid()
    {
        return isset($this->data[$this->key()]);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

}
