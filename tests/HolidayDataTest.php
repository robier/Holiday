<?php

use Robier\Holiday\HolidayData;

class HolidayDataTest extends PHPUnit_Framework_TestCase
{
    public function testMakingNewInstanceSuccess()
    {
        $object = new HolidayData(1, 1, 2015);

        $this->assertInstanceOf(HolidayData::class, $object);
    }

    public function instanceFromStringProvider()
    {
        return
            [
                ['2015-15-10', 'Y-d-m'],
                ['15-2015-10', 'd-Y-m'],
                ['15-10-2015', 'd-m-Y'],
                ['2015-10-15', 'Y-m-d'],
                ['10-15-2015', 'm-d-Y'],
                ['15-2015-10', 'm-Y-d'],
            ];
    }

    /**
     * @param $date
     * @param $format
     * @dataProvider instanceFromStringProvider
     */
    public function testMakingNewInstanceFromString($date, $format)
    {
        $holidayData = HolidayData::fromString($date, $format);

        $this->assertInstanceOf(HolidayData::class, $holidayData);

        $dateTime = DateTime::createFromFormat($format, $date);

        $this->assertEquals((int)$dateTime->format('d'), $holidayData->getDay());
        $this->assertEquals((int)$dateTime->format('m'), $holidayData->getMonth());
        $this->assertEquals((int)$dateTime->format('Y'), $holidayData->getYear());
    }




}
 