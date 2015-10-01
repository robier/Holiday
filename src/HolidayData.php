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
     * @param $date
     * @return $this
     */
    public static function fromString($date)
    {
        $date = explode('-', (string)$date);
        return new static($date[2], $date[1], $date[0]);
    }

    /**
     * @return \DateTime
     */
    public function toDateTimeObject()
    {
        $dateTime = new \DateTime();
        return $dateTime->setDate($this->getYear(), $this->getMonth(), $this->getDay());
    }

    /**
     * @return string
     */
    public function toString()
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
    }

}
