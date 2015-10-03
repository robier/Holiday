<?php

namespace Robier\Holiday\Collection;

use Robier\Holiday\Contract\Calculable;

class HolidayCollection extends Collection
{

    public function add(Calculable $calculation, $name)
    {
        return parent::add($calculation, $name);
    }

}