<?php

namespace Robier\Holiday\Contract;

use Robier\Holiday\HolidayData;

interface DateCalculatorInterface
{

    /**
     * Returns HolidayData object
     *
     * @param int $year
     * @return HolidayData
     */
    public function getDateFor($year);

}