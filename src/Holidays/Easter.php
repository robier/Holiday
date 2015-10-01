<?php

namespace Robier\Holiday\Holidays;

use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\HolidayData;

class Easter implements DateCalculatorInterface
{

    public function getDateFor($year)
    {
        /**
         * Magic
         *
         * @link http://codegolf.stackexchange.com/questions/11132/calculate-the-date-of-easter#answer-11135
         */
        $a = $year / 100 | 0;
        $b = $a >> 2;
        $c = ($year % 19 * 351 - ~($b + $a * 29.32 + 13.54) * 31.9) / 33 % 29 | 0;
        $d = 56 - $c - ~($a - $b + $c - 24 - $year / .8) % 7;
        $day = $d > 31 ? $d - 31 : $d;
        $month = $d > 31 ? 4 : 3;

        return new HolidayData($day, $month, $year);
    }
}