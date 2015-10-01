<?php

namespace Robier\Holiday\DayType;

use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\HolidayData;

class DependentDay implements DateCalculatorInterface
{

    /**
     * @var DateCalculatorInterface $date
     */
    protected $date;

    /**
     * @var int $add
     */
    protected $add;

    /**
     * @param DateCalculatorInterface $date
     * @param int $add
     */
    public function __construct(DateCalculatorInterface $date, $add)
    {
        $this->date = $date;

        $add = (string)$add;

        if ($add > 0) {
            $add = '+' . $add;
        }
        $this->add = $add;
    }

    public function getDateFor($year)
    {
        $string = sprintf('%s %s days', $this->date->getDateFor($year), $this->add);

        $time = strtotime($string);

        $day = date('d', $time);
        $month = date('m', $time);

        return new HolidayData($day, $month, $year);
    }
}