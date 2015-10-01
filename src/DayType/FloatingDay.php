<?php

namespace Robier\Holiday\DayType;

use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\HolidayData;

/**
 * Class FloatingDay
 */
class FloatingDay implements DateCalculatorInterface
{
    protected $string;

    /**
     * Can get string representation as for example:
     * - first monday of may
     * - third friday of september
     * - sixth monday of january
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = (string)$string;
    }

    public function getDateFor($year)
    {
        $string = $this->string;

        $time = strtotime($string . ' ' . $year);

        $day = date('d', $time);
        $month = date('m', $time);

        return new HolidayData($day, $month, $year);
    }
}