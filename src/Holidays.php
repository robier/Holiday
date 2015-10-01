<?php

namespace Robier\Holiday;

use DateInterval;
use DatePeriod;
use DateTime;
use Robier\Holiday\Collection\DayCollection;
use Robier\Holiday\Collection\HolidayCollection;
use Robier\Holiday\Collection\NameCollection;
use Robier\Holiday\Contract\DateCalculatorInterface;
use Robier\Holiday\DayType\DependentDay;
use Robier\Holiday\DayType\FixedDay;
use Robier\Holiday\DayType\FloatingDay;

class Holidays
{
    /**
     * @var HolidayCollection
     */
    protected $holidays;

    /**
     * @var DayCollection
     */
    protected $days;

    /**
     * @var NameCollection
     */
    protected $names;

    /**
     * Adding all collections to this object
     */
    public function __construct()
    {
        $this->holidays = new HolidayCollection();
        $this->days = new DayCollection();
        $this->names = new NameCollection();
    }

    /**
     * @param string $name
     * @param string $day
     * @param string $month
     * @return $this
     */
    public function registerFixed($name, $day, $month)
    {
        return $this->register($name, new FixedDay($day, $month));
    }

    /**
     * @param string $name
     * @param string $string
     * @return $this
     */
    public function registerFloating($name, $string)
    {
        return $this->register($name, new FloatingDay((string)$string));
    }

    /**
     * @param string $name
     * @param string $concrete
     * @param int $add
     * @return $this
     */
    public function registerDependent($name, $concrete, $add)
    {
        if (!$this->holidays->exists($concrete)) {
            throw new \InvalidArgumentException('There is no defined holiday with name ' . $concrete);
        }

        return $this->register($name, new DependentDay($this->holidays->get($concrete), $add));
    }

    /**
     * @param string $name
     * @param DateCalculatorInterface $date
     * @return $this
     */
    public function register($name, DateCalculatorInterface $date)
    {
        if ($this->holidays->exists($name)) {
            throw new \InvalidArgumentException('Holiday with name ' . $name . ' already exists!');
        }
        $this->holidays->add($date, $name);

        // when we register holidays we need to update already calculated years so
        // everything is up to date
        foreach ($this->days->years() as $year) {
            $this->calculateDay($date, $name, $year);
        }
        return $this;
    }

    /**
     * Calculate given years
     *
     * @param int $year,...
     * @return bool
     */
    protected function calculate($year)
    {
        $calculated = false;
        foreach (func_get_args() as $year) {
            /**
             * @var string $name
             * @var DateCalculatorInterface $day
             * @var HolidayData $holidayData
             */
            foreach ($this->holidays as $name => $day) {
                $this->calculateDay($day, $name, $year);
                $calculated = true;
            }
        }

        return $calculated;
    }

    /**
     * Calculates one day for provided year
     *
     * @param DateCalculatorInterface $date
     * @param string $name
     * @param int $year
     * @return $this
     */
    protected function calculateDay(DateCalculatorInterface $date, $name, $year)
    {
        $holidayData = $date->getDateFor($year);
        $holidayData->setName($name);

        // add holiday to days collection so we can easier get that holiday by date
        $this->days->add($holidayData);

        // add holiday to names collection so we can easier get that holiday by name
        $this->names->add($holidayData);

        return $this;
    }

    /**
     * Parameters you can send:
     * - isHolidayOn(21, 6, 2015);
     * - isHolidayOn('2015-06-21');
     * - isHolidayOn(new DateTime('2015-06-21'));
     *
     * @param string|int|DateTime $day
     * @param null|int $month
     * @param null|int $year
     * @return bool
     */
    public function isHolidayOn($day, $month = null, $year = null)
    {
        if ($day instanceof DateTime) {
            $year = $day->format('Y');
            $month = $day->format('m');
            $day = $day->format('d');
        }

        if (null === $month && null === $year) {
            $data = explode('-', (string)$day);
            $year = $data[0];
            $month = $data[1];
            $day = $data[2];

        } elseif (null === $year) {
            $year = date('Y');
        }

        $day = (int)$day;
        $month = (int)$month;
        $year = (int)$year;

        if ($this->days->exists($year) && !$this->days->exists($year, $month)) {
            return false;
        }

        if (!$this->days->exists($year)) {
            $this->calculate($year);
        }

        return $this->days->exists($year, $month, $day);
    }

    public function getHolidayOn($day, $month = null, $year = null)
    {
        if ($day instanceof DateTime) {
            $year = $day->format('Y');
            $month = $day->format('m');
            $day = $day->format('d');
        }

        if (null === $month && null === $year) {
            $data = explode('-', (string)$day);
            $year = $data[0];
            $month = $data[1];
            $day = $data[2];

        } elseif (null === $year) {
            $year = date('Y');
        }

        $day = (int)$day;
        $month = (int)$month;
        $year = (int)$year;

        if ($this->days->exists($year) && !$this->days->exists($year, $month)) {
            return null;
        }

        if (!$this->days->exists($year)) {
            $this->calculate($year);
        }

        return $this->days->get($year, $month, $day);
    }

    /**
     * Calculates dates between 2 dates
     *
     * @param string|DateTime $start
     * @param string|DateTime $end
     * @return array
     */
    protected function getDatesBetween($start, $end)
    {
        if (!$start instanceof DateTime) {
            $start = new DateTime((string)$start);
        }

        if (!$end instanceof DateTime) {
            $end = new DateTime((string)$end);
        }

        // DatePeriod do not work if we provide start date greater than end date
        if ($start->getTimestamp() > $end->getTimestamp()) {
            $tempStart = $start;
            $start = $end;
            $end = $tempStart;

            unset($tempStart);
        }

        $period = new DatePeriod(
            $start,
            new DateInterval('P1D'),
            $end
        );

        /** @var DateTime $date */
        foreach ($period as $date) {
            yield [(int)$date->format('d'), (int)$date->format('m'), (int)$date->format('Y')];
        }
    }

    /**
     * Check if there is any holiday registered between $startDate and $endDate
     *
     * @param string|DateTime $startDate
     * @param string|DateTime $endDate
     * @return bool
     */
    public function areAnyHolidaysBetween($startDate, $endDate)
    {
        foreach ($this->getDatesBetween($startDate, $endDate) as list($day, $month, $year)) {
            if (!$this->days->exists($year)) {
                $this->calculate($year);
            }

            if ($this->days->exists($year, $month, $day)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets all holidays that is between two dates
     *
     * @param string|DateTime $startDate
     * @param string|DateTime $endDate
     * @return array|\Robier\Holiday\HolidayData[]
     */
    public function getHolidaysBetween($startDate, $endDate)
    {
        $holidays = [];

        foreach ($this->getDatesBetween($startDate, $endDate) as list($day, $month, $year)) {
            if (!$this->days->exists($year)) {
                $this->calculate($year);
            }

            if ($this->days->exists($year, $month, $day)) {
                $holidays[] = $this->days->get($year, $month, $day);
            }
        }
        return $holidays;
    }

    /**
     * Gets HolidayData by name and year
     *
     * @param string $name
     * @param int $year
     * @return null|HolidayData
     */
    public function getHolidayByName($name, $year)
    {
        if (!$this->days->exists($year)) {
            $this->calculate($year);
        }

        return $this->names->get($name, $year);
    }

    /**
     * Returns array with all HolidayData for specific year
     *
     * @param int $year
     * @return HolidayData[]
     */
    public function getAllHolidaysFor($year)
    {
        $holidays = [];

        if (!$this->days->exists($year) && !$this->calculate($year)) {
            return $holidays;
        }

        foreach ($this->days->get($year) as $month) {
            foreach ($month as $day) {
                $holidays[] = $day;
            }
        };

        return $holidays;
    }

}