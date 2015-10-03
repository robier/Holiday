<?php

namespace Robier\Holiday\DayType;

use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\HolidayData;

class DependentDay implements Calculable
{

    /**
     * @var Calculable $date
     */
    protected $date;

    /**
     * @var int $add
     */
    protected $add;

    /**
     * @param Calculable $date
     * @param int $add
     */
    public function __construct(Calculable $date, $add)
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