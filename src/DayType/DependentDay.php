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
        $shift = sprintf('%s days', $this->add);

        $dateTime = $this->date->getDateFor($year)->toDateTimeObject();
        $dateTime->modify($shift);

        return new HolidayData($dateTime->format('d'), $dateTime->format('m'), $dateTime->format('Y'));
    }
}