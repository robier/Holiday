<?php

namespace Robier\Holiday\Holidays;

use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\HolidayData;

class Christmas implements Calculable
{

    public function getDateFor($year)
    {
        return new HolidayData(25, 12, $year);
    }
}