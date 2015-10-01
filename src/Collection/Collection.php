<?php

namespace Robier\Holiday\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;

abstract class Collection implements Countable, IteratorAggregate
{
    protected $collection = [];

    /**
     * Adds new value to collection
     *
     * @param $value
     * @param null|int|string $key
     * @return $this
     */
    public function add($value, $key = null)
    {
        $this->collection[$key] = $value;
        return $this;
    }

    /**
     * Remove collection item by key
     *
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        if ($this->exists($key)) {
            unset($this->collection[$key]);
            return true;
        }
        return false;
    }

    /**
     * Check if item with specific key exists
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->collection[$key]);
    }

    /**
     * Return collection item by key or null if it does not exist.
     *
     * @param $key
     * @return null|mixed
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            return $this->collection[$key];
        }
        return null;
    }

    /**
     * Get all collection items as array
     *
     * @return array
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * Get all items from collection except items with specific keys as array
     *
     * @param string|array $keys
     * @return array
     */
    public function allExcept($keys)
    {
        $args = (array)$keys;
        $data = $this->collection;

        foreach ($args as $value) {
            if (isset($data[$value])) {
                unset($data[$value]);
            }
        }
        return $data;
    }

    /**
     * Checking if collection have any items
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->collection);
    }

    /**
     * Clears the collection
     *
     * @return $this
     */
    public function clear()
    {
        $this->collection = [];
        return $this;
    }

    public function count()
    {
        return count($this->collection);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->collection);
    }

    public function keys()
    {
        return array_keys($this->collection);
    }
}