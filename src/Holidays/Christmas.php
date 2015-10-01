<?php

namespace Robier\Holiday\Holidays;

use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\HolidayData;

class Christmas implements DateCalculatorInterface
{

    public function getDateFor($year)
    {
        return new HolidayData(25, 12, $year);
    }
}