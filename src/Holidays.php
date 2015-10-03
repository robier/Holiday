<?php

namespace Robier\Holiday;

use DateInterval;
use DatePeriod;
use DateTime;
use Robier\Holiday\Collection\DayCollection;
use Robier\Holiday\Collection\HolidayCollection;
use Robier\Holiday\Collection\NameCollection;
use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\DayType\DependentDay;
use Robier\Holiday\DayType\FixedDay;
use Robier\Holiday\DayType\FloatingDay;

/**
 * Class Holidays
 *
 * Main class that are responsible to register holidays and handle them.
 *
 * @package Robier\Holiday
 */
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
     * Register fixed holiday providing day and month
     *
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
     * Register floating holiday providing string like "first Monday of September"
     *
     * @param string $name
     * @param string $string
     * @return $this
     */
    public function registerFloating($name, $string)
    {
        return $this->register($name, new FloatingDay((string)$string));
    }

    /**
     * Register dependant holiday by providing concrete holiday and how many days this holiday is after
     * (positive number) or before (negative number) concrete holiday
     *
     * @param string $name
     * @param string $concreteName
     * @param int $add
     * @return $this
     */
    public function registerDependent($name, $concreteName, $add)
    {
        if (!$this->holidays->exists($concreteName)) {
            throw new \InvalidArgumentException('There is no defined holiday with name ' . $concreteName);
        }

        return $this->register($name, new DependentDay($this->holidays->get($concreteName), $add));
    }

    /**
     * Registers calculator object into system
     *
     * @param string $name
     * @param Calculable $date
     * @return $this
     */
    public function register($name, Calculable $date)
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
             * @var Calculable $day
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
     * @param Calculable $date
     * @param string $name
     * @param int $year
     * @return $this
     */
    protected function calculateDay(Calculable $date, $name, $year)
    {
        $holidayData = $date->getDateFor($year);

        // set holiday name
        $holidayData->setName($name);

        // add holiday to days collection so we can easier get that holiday by date
        $this->days->add($holidayData);

        // add holiday to names collection so we can easier get that holiday by name
        $this->names->add($holidayData);

        return $this;
    }

    /**
     * Checks if there is any registered holiday on given date.
     * Parameters you can send:
     * - isHolidayOn(21, 6, 2015);                  -> all data provided
     * - isHolidayOn(21, 6);                        -> current year will be used if day and month are numbers
     * - isHolidayOn('2015-06-21', 'Y-m-d');        -> date and date format
     * - isHolidayOn(new DateTime('2015-06-21'));   -> DateTime as first argument
     *
     * @param string|int|DateTime $day
     * @param null|int $month
     * @param null|int $year
     * @return bool
     */
    public function isHolidayOn($day, $month = null, $year = null)
    {
        list($day, $month, $year) = $this->getDateFromArguments($day, $month, $year);

        if ($this->days->exists($year) && !$this->days->exists($year, $month)) {
            return false;
        }

        if (!$this->days->exists($year)) {
            $this->calculate($year);
        }

        return $this->days->exists($year, $month, $day);
    }

    /**
     * Returns HolidayData if there is any registered holiday on given date, null otherwise.
     * Parameters you can send for example:
     * - getHolidayOn(21, 6, 2015);                 -> all data provided
     * - getHolidayOn(21, 6);                       -> current year will be used if day and month are numbers
     * - getHolidayOn('2015-06-21', 'Y-m-d');       -> date and date format
     * - getHolidayOn(new DateTime('2015-06-21'));  -> DateTime as first argument
     *
     * @param string|int|DateTime $day
     * @param null|int $month
     * @param null|int $year
     * @return HolidayData|null
     */
    public function getHolidayOn($day, $month = null, $year = null)
    {
        list($day, $month, $year) = $this->getDateFromArguments($day, $month, $year);

        if ($this->days->exists($year) && !$this->days->exists($year, $month)) {
            return null;
        }

        if (!$this->days->exists($year)) {
            $this->calculate($year);
        }

        return $this->days->get($year, $month, $day);
    }

    /**
     * Calculates given arguments to day, month and year. Possible arguments for this function are:
     * - getDateFromArguments(new DateTime('2015-05-15'))   -> only DateTime object provided
     * - getDateFromArguments('2015-05-15', 'Y-m-d')        -> date and format
     * - getDateFromArguments(15, 5)                        -> current year will be used in this case
     * - getDateFromArguments(15, 5, 2015)                  -> all data provided
     *
     * @param int|string|DateTime $day
     * @param null|int $month
     * @param null|int $year
     * @return array
     */
    protected function getDateFromArguments($day, $month = null, $year = null)
    {
        if ($day instanceof DateTime) {
            $year = $day->format('Y');
            $month = $day->format('m');
            $day = $day->format('d');
        }

        if (null === $year) {
            if (is_string($day) && is_string($month)) {
                $dateTimeObject = DateTime::createFromFormat($month, $day);
                $year = $dateTimeObject->format('Y');
                $month = $dateTimeObject->format('m');
                $day = $dateTimeObject->format('d');
            } else {
                $year = date('Y');
            }
        }

        $day = (int)$day;
        $month = (int)$month;
        $year = (int)$year;

        return [$day, $month, $year];
    }

    /**
     * Calculates dates between 2 dates
     *
     * @param string|DateTime $start
     * @param string|DateTime $end
     * @param string $format
     * @return array
     */
    protected function getDatesBetween($start, $end, $format)
    {
        $format = (string)$format;

        if (!$start instanceof DateTime) {
            $start = DateTime::createFromFormat($format, (string)$start);
        }

        if (!$end instanceof DateTime) {
            $end = DateTime::createFromFormat($format, (string)$end);
        }

        // DatePeriod do not work if we provide start date greater than end date
        // so this is workaround
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
     * Check if there is any holiday registered between $startDate and $endDate, also you can provide date format
     *
     * @param string|DateTime $startDate
     * @param string|DateTime $endDate
     * @param string $format
     * @return bool
     */
    public function areAnyHolidaysBetween($startDate, $endDate, $format = 'Y-m-d')
    {
        foreach ($this->getDatesBetween($startDate, $endDate, $format) as list($day, $month, $year)) {
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
     * Gets all holidays that is between two dates, also you can provide date format
     *
     * @param string|DateTime $startDate
     * @param string|DateTime $endDate
     * @param string $format
     * @return array|\Robier\Holiday\HolidayData[]
     */
    public function getHolidaysBetween($startDate, $endDate, $format = 'Y-m-d')
    {
        $holidays = [];

        foreach ($this->getDatesBetween($startDate, $endDate, $format) as list($day, $month, $year)) {
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