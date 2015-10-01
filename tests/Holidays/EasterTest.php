<?php

class EasterTest extends PHPUnit_Framework_TestCase
{
    /**
     * All known easter dates
     * @link https://en.wikipedia.org/wiki/List_of_dates_for_Easter
     *
     * @return array
     */
    public function datesProvider()
    {
        return
            [
                [1995, 4, 16],
                [1996, 4, 7],
                [1997, 3, 30],
                [1998, 4, 12],
                [1999, 4, 4],
                [2000, 4, 23],
                [2001, 4, 15],
                [2002, 3, 31],
                [2003, 4, 20],
                [2004, 4, 11],
                [2005, 3, 27],
                [2006, 4, 16],
                [2007, 4, 8],
                [2008, 3, 23],
                [2009, 4, 12],
                [2010, 4, 4],
                [2011, 4, 24],
                [2012, 4, 8],
                [2013, 3, 31],
                [2014, 4, 20],
                [2015, 4, 5],
                [2016, 3, 27],
                [2017, 4, 16],
                [2018, 4, 1],
                [2019, 4, 21],
                [2020, 4, 12],
                [2021, 4, 4],
                [2022, 4, 17],
                [2023, 4, 9],
                [2024, 3, 31],
                [2025, 4, 20],
                [2026, 4, 5],
                [2027, 3, 28],
                [2028, 4, 16],
                [2029, 4, 1],
                [2030, 4, 21],
                [2031, 4, 13],
                [2032, 3, 28],
                [2033, 4, 17],
                [2034, 4, 9],
                [2035, 3, 25],
            ];
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @dataProvider datesProvider
     */
    public function testEaster($year, $month, $day)
    {
        $easter = new \Robier\Holiday\Holidays\Easter();

        $holiday = $easter->getDateFor($year);

        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $this->assertEquals($date, (string)$holiday);
        $this->assertTrue(checkdate($holiday->getMonth(), $holiday->getDay(), $holiday->getYear()));
    }
}
 