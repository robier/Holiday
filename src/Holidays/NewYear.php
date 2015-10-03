<?php

namespace Robier\Holiday\Holidays;

use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\HolidayData;

class NewYear implements Calculable
{

    public function getDateFor($year)
    {
        return new HolidayData(1, 1, $year);
    }
}