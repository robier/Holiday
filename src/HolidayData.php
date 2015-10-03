<?php

namespace Robier\Holiday;

class HolidayData
{

    protected $day;
    protected $month;
    protected $year;

    protected $name;

    public function __construct($day, $month, $year)
    {
        $this->day = (int)$day;
        $this->month = (int)$month;
        $this->year = (int)$year;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
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
