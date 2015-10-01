<?php

namespace Robier\Holiday\Holidays;

use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\HolidayData;

class NewYear implements DateCalculatorInterface
{

    public function getDateFor($year)
    {
        return new HolidayData(1, 1, $year);
    }
}