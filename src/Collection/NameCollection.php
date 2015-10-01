<?php

namespace Robier\Holiday\Collection;

use Robier\Holiday\HolidayData;

class NameCollection extends Collection
{
    public function add(HolidayData $holidayData)
    {
        $this->collection[$holidayData->getName()][$holidayData->getYear()] = $holidayData;

        return $this;
    }

    public function remove($name)
    {
        if ($this->exists($name)) {
            unset($this->collection[$name]);
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @param null|int $year
     * @return bool
     */
    public function exists($name, $year = null)
    {
        if (!isset($this->collection[$name])) {
            return false;
        }

        if (null === $year) {
            return isset($this->collection[$name]);
        }
        return isset($this->collection[$name][$year]);
    }

    /**
     * @param string $name
     * @param int $year
     * @return HolidayData[]|HolidayData|null
     */
    public function get($name, $year = null)
    {
        if (!$this->exists($name, $year)) {
            return null;
        }

        if (null === $year) {
            $this->collection[$name];
        }

        return $this->collection[$name][$year];
    }
}