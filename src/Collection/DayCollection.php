<?php

namespace Robier\Holiday\Collection;

use Robier\Holiday\HolidayData;

class DayCollection extends Collection
{

    public function add(HolidayData $holidayData)
    {
        $this->collection[$holidayData->getYear()][$holidayData->getMonth()][$holidayData->getDay()] = $holidayData;

        ksort($this->collection[$holidayData->getYear()][$holidayData->getMonth()]);
        ksort($this->collection[$holidayData->getYear()]);
        ksort($this->collection);

        return $this;
    }

    public function remove($year, $month = null, $day = null)
    {
        if (null === $month && null === $day) {
            if ($this->exists($year)) {
                unset($this->collection[$year]);
                return true;
            }
            return false;
        }

        if (null === $day) {
            if ($this->exists($year, $month)) {
                unset($this->collection[$year][$month]);
                return true;
            }
            return false;
        }

        if ($this->exists($year, $month, $day)) {
            unset($this->collection[$year][$month][$day]);
            return true;
        }
        return false;
    }

    public function exists($year, $month = null, $day = null)
    {
        if (null === $month && null === $day) {
            return isset($this->collection[$year]);
        }

        if (null === $day) {
            return isset($this->collection[$year][$month]);
        }

        return isset($this->collection[$year][$month][$day]);
    }

    /**
     * @param int $year
     * @param null|int $month
     * @param null|int $day
     * @return HolidayData[]|HolidayData|null
     */
    public function get($year, $month = null, $day = null)
    {
        if (!$this->exists($year, $month, $day)) {
            return null;
        }

        if (null === $month && null === $day) {
            return $this->collection[$year];
        }

        if (null === $day) {
            return $this->collection[$year][$month];
        }

        return $this->collection[$year][$month][$day];
    }

    /**
     * @return array
     */
    public function years()
    {
        return parent::keys();
    }
}