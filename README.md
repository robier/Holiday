Holidays
========

Holidays project will solve you a problem of calculating holidays. No matter if they are 
fixed dates, floating dates or dates that depends of some other holiday. This library
was developed in need for holiday calculation that is not fixed to specific country as
almost any other library out there is.

Note that library do not support holidays that last more than 1 day.

And it's easy to use:

```PHP
use \Robier\Holiday\Holidays;

$holidays = new Holidays();
$holidays->registerFixed('new_year', 1, 1);
$holidays->registerFloating('us_labour_day', 'first Monday of September');
$holidays->register('easter', new Holidays\Easter());
// Ascension of Jesus accords 40 days after easter
$holidays->registerDependent('ascension_of_jesus', 'easter', 40);

// true if there is a holiday on that date, false otherwise
var_dump($holidays->isHolidayOn(1, 1, 2030));

// gets HolidayData object if holiday exists on that date, null otherwise
var_dump($holidays->getHolidayOn(1, 1, 2030));

// true if there is any holidays in that period, false otherwise
var_dump($holidays->areAnyHolidaysBetween('2018-07-04', '2018-09-15'));

// array of all holidays
var_dump($holidays->getAllHolidaysFor(2015));
```

Also we really discourage use of timestamps with this library, we do not like them
because they are really not precise with time zones and how PHP deals with them :)

Features
--------

- you can register 3 types of dates:
    - `fixed` where a holiday's date is always fixed (like new year)
    - `floating` where a holiday's date is not fixed (like easter)
    - `dependant` where one holiday depends upon some other holiday (it's `x` days before 
    or after "main" holiday)
- you can check if holiday exist providing date
- also you can get holiday by providing date
- you can check if there is any holidays in date range
- also you can get holiday by providing date range
- you can get all dates for specified year
- you can make you own holiday class definitions by just implementing `DateCalculatorInterface`
(it can not be dependant date type)

Installation
------------

This project requires PHP 5.5 or. higher.
Installation of this project is simple you just need to add one line to your `composer.json` file

    {
        "require": {
            "robier/holiday": "*"
        }
    }

and run command

    composer update

Contribute
----------

You can contribute via pull request.

Todo
----

- [ ] add support for holidays that last more than 1 day
- [ ] add more default holidays
- [ ] add more test coverage

Licence
-------

This project is developed under MIT licence.
