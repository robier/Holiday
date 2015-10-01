<?php

namespace Robier\Holiday\Collection;

use Robier\Holiday\Contract\DateCalculatorInterface;

class HolidayCollection extends Collection
{

    public function add(DateCalculatorInterface $calculation, $name)
    {
        return parent::add($calculation, $name);
    }

}