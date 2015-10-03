<?php

namespace Robier\Holiday\DayType;

use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\HolidayData;

class FixedDay implements Calculable
{

    protected $day;
    protected $month;

    /**
     * Make a fixed date.
     *
     * @param int $day
     * @param int $month
     */
    public function __construct($day, $month)
    {
        $this->day = (int)$day;
        $this->month = (int)$month;
    }

    public function getDateFor($year)
    {
        return new HolidayData($this->day, $this->month, $year);
    }
}