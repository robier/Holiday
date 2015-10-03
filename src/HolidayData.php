<?php

namespace Robier\Holiday;

use DateTime;

/**
 * Class HolidayData
 *
 * Simple value object for holding holiday related data as day, month, year and holiday name.
 *
 * @package Robier\Holiday
 */
class HolidayData
{

    protected $day;
    protected $month;
    protected $year;

    protected $name;

    /**
     * @param int $day
     * @param int $month
     * @param int $year
     */
    public function __construct($day, $month, $year)
    {
        $this->day = (int)$day;
        $this->month = (int)$month;
        $this->year = (int)$year;
    }

    /**
     * Sets holiday name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * Returns holiday name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns day
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Returns month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Returns year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Returns string representation of HolidayData in ISO 8601 standard (Y-m-d)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Creates HolidayData from string
     *
     * @param string $date
     * @param string $format
     * @return $this
     */
    public static function fromString($date, $format = 'Y-m-d')
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        return new static($dateTime->format('d'), $dateTime->format('m'), $dateTime->format('Y'));
    }

    /**
     * Returns DateTime representation of HolidayData object
     *
     * @return DateTime
     */
    public function toDateTimeObject()
    {
        $dateTime = new DateTime();
        return $dateTime->setDate($this->getYear(), $this->getMonth(), $this->getDay());
    }

    /**
     * Returns string representation of HolidayData object
     *
     * @param string $format
     * @return string
     */
    public function toString($format = 'Y-m-d')
    {
        $dateTime = new DateTime();
        $dateTime->setDate($this->year, $this->month, $this->day);

        return $dateTime->format($format);
    }

}
