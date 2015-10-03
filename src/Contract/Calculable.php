<?php

namespace Robier\Holiday\Contract;

use Robier\Holiday\HolidayData;

interface Calculable
{

    /**
     * Returns HolidayData object
     *
     * @param int $year
     * @return HolidayData
     */
    public function getDateFor($year);

}