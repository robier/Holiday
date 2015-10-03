<?php

use Robier\Holiday\Contract\Calculable;
use Robier\Holiday\Holidays;

class HolidaysTest extends PHPUnit_Framework_TestCase
{
    public function fixedDataProvider()
    {
        return
            [
                ['test1', 20 ,12],
                ['test2', 15, 1],
                ['test3', 1, 1],
                ['test4', 2, 10],
                ['test5', 19, 4],
                ['test6', 8, 9],
                ['test7', 9, 9],
            ];
    }

    /**
     * @param $name
     * @param $day
     * @param $month
     *
     * @dataProvider fixedDataProvider
     */
    public function testFixedHoliday($name, $day, $month)
    {
        $holidays = new Holidays();
        $holidays->registerFixed($name, $day, $month);

        foreach(range(1900, 2050, 1) as $year){
            $this->assertTrue($holidays->isHolidayOn($day, $month, $year));

            $holiday = $holidays->getHolidayByName($name, $year);

            $this->assertInstanceOf(\Robier\Holiday\HolidayData::class, $holiday);

            $this->assertEquals($name, $holiday->getName());

            $this->assertEquals(sprintf('%04d-%02d-%02d', $year, $month, $day), (string)$holiday);

            $this->assertTrue($holidays->isHolidayOn($holiday->getDay(), $holiday->getMonth(), $holiday->getYear()));

            $this->assertTrue(checkdate($holiday->getMonth(), $holiday->getDay(), $holiday->getYear()));
        }
    }

    public function floatingDataProvider()
    {
        return
            [
                ['test1', 'first Monday of May', 1],
                ['test2', 'first Tuesday of May', 2],
                ['test3', 'first Wednesday of May', 3],
                ['test4', 'first Thursday of May', 4],
                ['test5', 'third Friday of May', 5],
                ['test6', 'first Saturday of May', 6],
                ['test7', 'second Sunday of May', 7],
            ];
    }

    /**
     * @param string $name
     * @param string $string
     * @param int $dayOfWeek
     *
     * @dataProvider floatingDataProvider
     */
    public function testFloatingHoliday($name, $string, $dayOfWeek)
    {
        $holidays = new Holidays();
        $holidays->registerFloating($name, $string);

        foreach(range(1970, 2020, 1) as $year){

            $holiday = $holidays->getHolidayByName($name, $year);

            $this->assertInstanceOf(\Robier\Holiday\HolidayData::class, $holiday);

            // we need to check if we get real date not default one
            $this->assertTrue((string)$holiday !== '1970-01-01');

            // we need to check if we got real day of week
            $this->assertEquals(date('N', strtotime($string . ' '. $year)), $dayOfWeek);

            $this->assertTrue($holidays->isHolidayOn($holiday->getDay(), $holiday->getMonth(), $holiday->getYear()));

            $this->assertTrue(checkdate($holiday->getMonth(), $holiday->getDay(), $holiday->getYear()));
        }
    }

    public function predefinedDateDataProvider()
    {
        return
            [
                ['christmas', new Holidays\Christmas()],
                ['easter', new Holidays\Easter()],
                ['new_year', new Holidays\NewYear()],
            ];
    }

    /**
     * @param $name
     * @param Calculable $holiday
     *
     * @dataProvider predefinedDateDataProvider
     */
    public function testPredefinedHoliday($name, Calculable $holiday)
    {
        $holidays = new Holidays();
        $holidays->register($name, $holiday);

        foreach(range(1970, 2020, 1) as $year){

            $holiday = $holidays->getHolidayByName($name, $year);

            $this->assertInstanceOf(\Robier\Holiday\HolidayData::class, $holiday);

            if($name == 'new_year'){
                // we know that new year always fall on first day in year
                $this->assertEquals($year.'-01-01', (string)$holiday);
            }elseif($name == 'christmas') {
                // we know that christmas always fall on 25th of December
                $this->assertEquals($year.'-12-25', (string)$holiday);
            }

            $this->assertTrue($holidays->isHolidayOn($holiday->getDay(), $holiday->getMonth(), $holiday->getYear()));

            $this->assertTrue(checkdate($holiday->getMonth(), $holiday->getDay(), $holiday->getYear()));
        }
    }

    public function dependentDateDataProvider()
    {
        return
            [
                ['test1', 1, new Holidays\Christmas()],
                ['test2', 2, new Holidays\Christmas()],
                ['test3', -3, new Holidays\Christmas()],
                ['test4', -5, new Holidays\Christmas()],
            ];
    }

    /**
     * @param $name
     * @param $days
     * @param $concrete
     *
     * @dataProvider dependentDateDataProvider
     */
    public function testDependentDay($name, $days, $concrete)
    {
        $holidays = new Holidays();
        $holidays->register('master', $concrete);
        $holidays->registerDependent($name, 'master', $days);

        foreach(range(1970, 2020, 1) as $year){

            $holiday = $holidays->getHolidayByName($name, $year);
            $masterHoliday = $holidays->getHolidayByName('master', $year);

            $this->assertInstanceOf(\Robier\Holiday\HolidayData::class, $holiday);
            $this->assertInstanceOf(\Robier\Holiday\HolidayData::class, $masterHoliday);

            $this->assertEquals($masterHoliday->getYear(), $holiday->getYear());
            $this->assertEquals($masterHoliday->getMonth(), $holiday->getMonth());
            $this->assertEquals($masterHoliday->getDay() + $days, $holiday->getDay());

            $this->assertTrue($holidays->isHolidayOn($masterHoliday->getDay() + $days, $masterHoliday->getMonth(),$masterHoliday->getYear()));
        }
    }

    public function testDependantDayException()
    {
        $holidays = new Holidays();
        $holidays->register('master', new Holidays\Easter());

        $this->setExpectedException(\InvalidArgumentException::class);

        $holidays->registerDependent('test', 'test3', 5);
    }

    public function testGettingAllHolidays()
    {
        $holidays = new Holidays();

        $this->assertCount(0, $holidays->getAllHolidaysFor(2015));

        $holidays->register('test', new Holidays\Christmas());

        $this->assertCount(1, $holidays->getAllHolidaysFor(2015));

        $holidays->register('test2', new Holidays\Easter());

        $this->assertCount(2, $holidays->getAllHolidaysFor(2015));

        $holidays->register('test3', new Holidays\NewYear());

        $this->assertCount(3, $holidays->getAllHolidaysFor(2015));

        $this->assertCount(3, $holidays->getAllHolidaysFor(2014));
        $this->assertCount(3, $holidays->getAllHolidaysFor(2016));
    }

    public function testDuplicateHolidayFail()
    {
        $holidays = new Holidays();

        $holidays->register('easter', new Holidays\Easter());
        $this->setExpectedException(\InvalidArgumentException::class);

        $holidays->register('easter', new Holidays\Christmas());
    }

    public function testDateRangeCheck()
    {
        $holidays = new Holidays();

        $holidays->register('new_year', new Holidays\NewYear());
        $this->assertFalse($holidays->areAnyHolidaysBetween('2015-01-02', '2015-12-31'));
        $this->assertTrue($holidays->areAnyHolidaysBetween('2015-12-31', '2016-01-02'));

        $this->assertCount(0, $holidays->getHolidaysBetween('2017-01-02', '2017-12-31'));
        $this->assertCount(1, $holidays->getHolidaysBetween('2018-12-31', '2019-01-02'));

        $this->assertCount(5, $holidays->getHolidaysBetween('2015-12-31', '2020-01-02'));
    }



    public function testNotExistingHoliday()
    {
        $holidays = new Holidays();

        $period = new DatePeriod(
            new DateTime('2015-01-01'),
            new DateInterval('P1D'),
            new DateTime('2015-12-31')
        );

        /** @var DateTime $day */
        foreach ($period as $day) {
            $this->assertFalse($holidays->isHolidayOn($day->format('d'), $day->format('m'), $day->format('Y')));
        }
    }
}
