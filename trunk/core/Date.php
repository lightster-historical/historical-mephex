<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * generates a formatted date/time and allows for basic date operations
 *
 * Date represents a certain date and time, which can be set upon creation of
 * the object or using the set method. If not set, the date/time is assumed
 * to be the current date/time.
 *
 * In addition, the class provides a way to add years, months, etc. to the
 * represented date/time. There is also a method for finding the difference
 * between two dates.
 *
 * PHP version 5
 *
 * LICENSE: This file may only be used under the terms of the license
 *          available at www.mephex.com.
 *
 * @author     Matt Light <mlight@@mephex..com>
 * @copyright  2006 Mephex Technologies
 * @license    http://www.mephex.com
 * @link
 * @since      05.07.01
 * @version    05.12.21
 */


/**
 * @dependency com.mephex.core.Function
 */
//require_once PATH_LIB . 'com/mephex/core/Function.php';


class Date
{
    // array of abbreviated titles of the days of the week
    static protected $dayOfWk
        = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    // array of titles of the days of the week
    static protected $dayOfWeek
        = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'
        , 'Friday', 'Saturday');
    // array of abbreviated titles of the month of the year
    static protected $monOfYr
        = array(1=>'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug',
        'Sep', 'Oct', 'Nov', 'Dec');
    // array of titles of the month of the year
    static protected $monOfYear
        = array(1=>'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December');


    protected $yr;        // integer year (0+)
    protected $mon;       // integer month of the year (1 to 12)
    protected $day;       // integer day of the month (1 to 28, 29, 30, or 31,
                          // depending on the month and year)
    protected $hr;        // integer hour of the day (0 to 23)
    protected $min;       // integer minute of the hour (0 to 59)
    protected $sec;       // integer second of the minute (0 to 59)
    protected $tz;        // float time zone (-13 to 13)


    //*********************************************************************************
    //  name:       __construct
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  initializes the instance variables
    //  NOTE: __construct() takes any parameters that Date::parse takes
    //*********************************************************************************
    //  @param  SEE NOTE
    //  @return void
    //*********************************************************************************
    public function __construct()
    {
        // if the first argument is an array of arguments
        if (func_num_args() > 0 && is_array(func_get_arg(0)))
        {
            $args = func_get_arg(0);
        }
        // otherwise, use all of the function arguments
        else
        {
            $args = func_get_args();
        }

        // set the time
        $this->set($this->parse($args));
    }
    // constructor

    //  return individual date attributes
    public function getYear()          {return $this->yr;}
    public function getMonth()         {return $this->mon;}
    public function getDay()           {return $this->day;}
    public function getHour()          {return $this->hr;}
    public function getMinute()        {return $this->min;}
    public function getSecond()        {return $this->sec;}
    public function getTimeZone()      {return $this->tz;}


    public function setHour($hr)
    {
        if(0 <= $hr && $hr < 24)
            $this->hr = $hr;
    }

    public function setMinute($min)
    {
        if(0 <= $min && $min < 60)
            $this->min = $min;
    }



    public function compareTo(Date $date)
    {
        $left = new Date($this);
        $right = new Date($date);

        if(is_null($right))
            return null;

        $left->changeMinute(-$left->tz * 60);
        $right->changeMinute(-$right->tz * 60);

        #echo '<br/><br/>';
        #print_r($left);
        #echo '<br/>';
        #print_r($right);

        $leftIsGreater = 1;

        if($left->yr > $right->yr)
            return $leftIsGreater;
        else if($left->yr < $right->yr)
            return -$leftIsGreater;
        else if($left->mon > $right->mon)
            return $leftIsGreater;
        else if($left->mon < $right->mon)
            return -$leftIsGreater;
        else if($left->day > $right->day)
            return $leftIsGreater;
        else if($left->day < $right->day)
            return -$leftIsGreater;
        else if($left->hr > $right->hr)
            return $leftIsGreater;
        else if($left->hr < $right->hr)
            return -$leftIsGreater;
        else if($left->min > $right->min)
            return $leftIsGreater;
        else if($left->min < $right->min)
            return -$leftIsGreater;
        else if($left->sec > $right->sec)
            return $leftIsGreater;
        else if($left->sec < $right->sec)
            return -$leftIsGreater;
        else
            return 0;
    }

    public function isBefore(Date $date)
    {
        return ($this->compareTo($date) < 0);
    }

    public static function getMax()
    {
        $args = func_get_args();
        $dates = self::getDatesFromArgs($args);

        $max = null;
        foreach($dates as $date)
        {
            if(is_null($max))
                $max = $date;
            else if($max->compareTo($date) < 0)
                $max = $date;
        }

        return $max;
    }

    public static function getMin()
    {
        $args = func_get_args();
        $dates = self::getDatesFromArgs($args);

        $min = null;
        foreach($dates as $date)
        {
            if(is_null($min))
                $min = $date;
            else if($min->compareTo($date) > 0)
                $min = $date;
        }

        return $min;
    }

    protected static function getDatesFromArgs($args)
    {
        $dates = array();
        foreach($args as $arg)
        {
            if(is_array($arg))
            {
                foreach($arg as $date)
                {
                    if($date instanceof Date)
                        $dates[] = $date;
                }
            }
            else if($arg instanceof Date)
                $dates[] = $arg;
        }

        return $dates;
    }


    //*********************************************************************************
    //  name:       getDaysInMon
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  returns the number of days in a particular month of a particular year
    //*********************************************************************************
    //  @param
    //      integer year
    //      integer month
    //  @return integer daysInMon
    //*********************************************************************************
    public function getDaysInMon($yr, $mon)
    {
        // set the number of days in each month
        $dayPerMon  = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        // if it is a leap year, the second month has 29 days
        if($yr % 4 == 0 && ($yr % 100 != 0 || $yr % 400 == 0))
            $dayPerMon[1] = 29;

        // if the month is valid, return the days in the month
        if(1 <= $mon && $mon <= 12)
        {
            return $dayPerMon[$mon - 1];
        }
        // otherwise, return unknown
        else
        {
            return null;
        }
    }
    // getDaysInMon method

    //*********************************************************************************
    //  name:       getDaysInYr
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  returns the number of days in a particular year
    //*********************************************************************************
    //  @param  integer year
    //  @return integer daysInYr
    //*********************************************************************************
    public function getDaysInYr($yr)
    {
        return ($yr % 4 == 0 && ($yr % 100 != 0 || $yr % 400 == 0)) ? 366 : 365;
    }
    // getDaysInYr method

    //*********************************************************************************
    //  name:       getNumSuffix
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  returns the suffix for the number (e.g. st, nd, rd, th)
    //*********************************************************************************
    //  @param
    //      integer num     the number
    //      integer append  whether or not to return the number with the suffix
    //  @return
    //      string  suffix  the suffix for a number, possibly preceded by the number
    //*********************************************************************************
    public function getNumSuffix ($num, $append = null)
    {
        // pick off the ones and tens column
        $tens    = $num % 100;
        $ones    = $num % 10;
        $tens   -= $ones;
        $tens   /= 10;

        // if the tens column is 1
        if ($tens == 1)
        {
            $suffix = 'th';
        }
        // otherwise
        else
        {
            switch ($ones)
            {
                // if the ones column is 1
                case 1:
                    $suffix = 'st';
                break;
                // if the ones column is 2
                case 2:
                    $suffix = 'nd';
                break;
                // if the ones column is 3
                case 3:
                    $suffix = 'rd';
                break;
                // otherwise
                default:
                    $suffix = 'th';
                break;
            }
        }

        // if the suffix should be appended to the number, return the number
        // with the suffix appended
        if ($append)
        {
            return $num . $suffix;
        }
        // otherwise, return the suffix
        else
        {
            return $suffix;
        }
    }
    // getNumSuffix method

    //*********************************************************************************
    //  name:       set
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  sets the date/time
    //  NOTE: set() takes any parameters that Date::parse takes
    //*********************************************************************************
    //  @param  SEE NOTE
    //  @return void
    //*********************************************************************************
    public function set()
    {
        // if the argument is an array generated by parse()
        $arg = func_get_arg(0);
        if (is_array($arg) && count($arg) == 7)
        {
            $args = $arg;
        }
        // otherwise, parse the arguments for a date
        else
        {
            $args = func_get_args();
        }

        // parse the date arguments and record the resulting date properties
        list($this->yr, $this->mon, $this->day, $this->hr, $this->min,
            $this->sec, $this->tz) = $this->parse($args);

        $this->simplifyInternal();
    }
    // set method


    //*********************************************************************************
    //  name:       setDayOfWeek
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  sets the short and long name of a day of a week
    //*********************************************************************************
    //  @param
    //      integer index   number of the day of the week to modify
    //      string  short   short version of the name of the day
    //      string  long    full version of the name of the day
    //  @return bool
    //      true    if the index is valid
    //      false   if the index is invalid
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function setDayOfWeek ($index, $short, $long)
    {
        // if the index is valid
        if (1 <= $index && $index <= 7)
        {
            // set the day of week titles
            $this->dayOfWk[$index - 1]  = $short;
            $this->dayOfWeek[$index - 1]    = $long;

            return true;
        }
        else
        {
            return false;
        }
    }
    // setDayOfWeek method

    //*********************************************************************************
    //  name:       setMonOfYear
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  sets the short and long name of a month
    //*********************************************************************************
    //  @param
    //      integer index   number of the month of the year to modify
    //      string  short   short version of the name of the month
    //      string  long    full version of the name of the month
    //  @return bool
    //      true    if the index is valid
    //      false   if the index is invalid
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function setMonOfYear ($index, $short, $long)
    {
        // if the index is valid
        if (1 <= $index && $index <= 12)
        {
            // set the month of year titles
            $this->monOfYr[$index]      = $short;
            $this->monOfYear[$index]    = $long;

            return true;
        }
        else
        {
            return false;
        }
    }
    // setMonOfYear method

    //*********************************************************************************
    //  name:       simplify
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  simplifies the date/time by changing 12 months into a year, 60 minutes into
    //  an hour, etc.
    //*********************************************************************************
    //  @param
    //      integer yr              year
    //      integer mon             month of the year
    //      integer day             day of the month
    //      integer hr              hour of the day
    //      integer min             minute of the hour
    //      integer sec             second of the minute
    //  @return
    //      integer yr              year
    //      integer mon             month of the year (1 to 12)
    //      integer day             day of the month (1 to 28, 29, 30, or 31)
    //      integer hr              hour of the day (0 to 23)
    //      integer min             minute of the hour (0 to 59)
    //      integer sec             second of the minute (0 to 59)
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function simplify ($yr, $mon, $day, $hr, $min, $sec)
    {
        // if there are more seconds than allowable
        if ($sec > 59)
        {
            // convert sets of 60 seconds into minutes
            $min    += floor($sec / 60);
            $sec    %= 60;
        }
        // if there are less seconds than allowable
        else if ($sec < 0)
        {
            // convert minutes into sets of 60 seconds
            $min    -= ceil(-$sec / 60);
            $sec     = -$sec % 60;

            if ($sec != 0)
                $sec    = 60 - $sec;
        }

        // if there are more minutes than allowable
        if ($min > 59)
        {
            // convert the extra minutes into hours
            $hr     += floor($min / 60);
            $min    %= 60;
        }
        // if there are more minutes than allowable
        else if ($min < 0)
        {
            // convert hours into sets of 60 minutes
            $hr     -= ceil(-$min / 60);
            $min     = -$min % 60;

            if ($min != 0)
                $min    = 60 - $min;
        }

        // if there are more months than allowable
        if ($mon > 12)
        {
            // make the month a zero-based number
            $mon--;

            // convert the extra months into years
            $yr     += floor($mon / 12);
            $mon     = $mon % 12;

            $mon++;
        }
        // if there are less months than allowable
        else if ($mon < 1)
        {
            // convert months into sets of 12 hours
            $yr     -= ceil(-$mon / 12);
            $mon     = -$mon % 12;

            $mon    = 12 - $mon;
        }

        // if the day is zero, the day should be set to the last day of the month
        if ($day == 0)
        {
            $day    = $this->getDaysInMon($yr, $mon);
        }

        // store a copy of the current day
        $pDay   = $day;
        // if there are more hours than allowable, convert the extra hours into days
        if ($hr > 23)
        {
            $day    += floor($hr / 24);
            $hr     %= 24;

            // if the day moved from negative to positive, add one because zero
            // should not be counted
            if ($pDay < 0 && $day >= 0)
            {
                $day++;
            }
        }
        // if there are less hours than allowable, convert days into sets of
        // 24 hours
        else if ($hr < 1)
        {
            $day    -= ceil(-$hr / 24);
            $hr      = -$hr % 24;

            if ($hr != 0)
                $hr = 24 - $hr;
        }
        unset($pDay);

        // while there are too many days
        while ($day > $this->getDaysInMon($yr, $mon))
        {
            $day -= $this->getDaysInMon($yr, $mon);

            if (++$mon > 12)
            {
                $yr++;
                $mon -= 12;
            }
        }
        // while there are too few days
        while ($day < 1)
        {
            if (--$mon < 1)
            {
                $yr--;
                $mon += 12;
            }

            $day += $this->getDaysInMon($yr, $mon);
        }

        //
        return array(intval($yr), intval($mon), intval($day), intval($hr),
            intval($min), intval($sec));
    }
    // simplify method

    //*********************************************************************************
    //  name:       simplifyInternal
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  simplifies the date/time by changing 12 months into a year, 60 minutes into
    //  an hour, etc.
    //*********************************************************************************
    //  @param  void
    //  @return void
    //*********************************************************************************
    protected function simplifyInternal ()
    {
        list($this->yr, $this->mon, $this->day, $this->hr, $this->min,
            $this->sec)  = $this->simplify($this->yr, $this->mon, $this->day,
            $this->hr, $this->min, $this->sec);
    }
    // simplifyInternal method

    //*********************************************************************************
    //  name:       simplifyDiff
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //
    //*********************************************************************************
    //  @param  void
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function simplifyDiff ($diffYr, $diffMon, $diffDay, $diffHr, $diffMin,
        $diffSec, $endYr = 0, $endMon = 0)
    {
        // if the year difference is negative
        if ($diffYr < 0)
        {
            // the dates were subtracted in the wrong order (min - max), so
            // multiply all components by -1 to fix this
            $diffYr     *= -1;
            $diffMon    *= -1;
            $diffDay    *= -1;
            $diffMon    *= -1;
            $diffHr     *= -1;
            $diffSec    *= -1;
        }
        //2007/2/24-2007/5/25=0/-3/-1
        //0/9/-1
        //0/8/30
        // if the month difference is negative
        else if ($diffMon < 0)
        {
            // make the month difference positive by adding 12 months
            $diffMon    += 12;

            // and if the year is greater than zero, subtract a year
            if ($diffYr > 0)
            {
                $diffYr--;
            }
        }
        // if the day difference is negative
        else if ($diffDay < 0)
        {
            // if the month is not January, use the number of days from the
            // previous month
            if ($endMon != 0)
                $diffDay    += $this->getDaysInMon($endYr, $endMon - 1);
            // if the month is January, use 31 days (the number of days in
            // December)
            else
            {
                $diffDay    += 31;
            }

            // a month worth of days was added, so compensate this by
            // subtracting a month
            $diffMon--;
        }
        // if the hour difference is negative
        else if ($diffHr < 0)
        {
            // make the hour difference positive by adding 24 hours
            $diffHr     += 24;

            // a day worth of hours was added, so compensate this by subtracting
            // a day
            $diffDay--;
        }
        // if the minute difference is negative
        else if ($diffMin < 0)
        {
            // make the minute difference positive by adding 60 minutes
            $diffMin    += 60;

            // an hour worth of minutes was added, so compensate this by
            // subtracting an hour
            $diffHr--;
        }
        // if the second difference is negative
        else if ($diffSec < 0)
        {
            // make the second difference positive by adding 60 seconds
            $diffSec    += 60;

            // a minute worth of seconds was added, so compensate this by
            // subtracting a minute
            $diffMin--;
        }
        // otherwise, there was no change so return the array
        else
        {
            return array($diffYr, $diffMon, $diffDay, $diffHr, $diffMin,
                $diffSec);
        }

        // check to see if the difference needs to be further simplified
        return $this->simplifyDiff($diffYr, $diffMon, $diffDay, $diffHr,
            $diffMin, $diffSec, $endYr, $endMon);
    }
    // simplifyDiff method


    //*********************************************************************************
    //  name:       parse
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  parses one of many date forms into an array of date components
    //*********************************************************************************
    //  @param
    //      Date    date            Date object to copy
    //      integer timeZone = 0    time zone to set (NOTE: this does not convert
    //                              the time to the new time zone; this simply
    //                              sets the time zone)
    //  @param
    //      integer timeStamp       unix timestamp
    //      integer timeZone = 0    same as the above timeZone
    //  @param
    //      string  sqlStamp        current date and/or time
    //      integer timeZone = 0    same as the above $timeZone
    //  @param
    //      integer[0]              year
    //      integer[1]              month of the year (1 to 12)
    //      integer[2]              day of the month (1 to 28, 29, 30, or 31)
    //      integer[3]              hour of the day (0 to 23)
    //      integer[4]              minute of the hour (0 to 59)
    //      integer[5]              second of the minute (0 to 59)
    //      integer[6]              time zone
    //  @param
    //      integer yr              year
    //      integer mon             month of the year (1 to 12)
    //      integer day             day of the month (1 to 28, 29, 30, or 31)
    //  @param
    //      integer yr              year
    //      integer mon             month of the year (1 to 12)
    //      integer day             day of the month (1 to 28, 29, 30, or 31)
    //      integer hr              hour of the day (0 to 23)
    //      integer min             minute of the hour (0 to 59)
    //      integer sec             second of the minute (0 to 59)
    //      integer timeZone = 0    same as the above $timeZone
    //  @return integer[7]
    //      integer[0]              year
    //      integer[1]              month of the year (1 to 12)
    //      integer[2]              day of the month (1 to 28, 29, 30, or 31)
    //      integer[3]              hour of the day (0 to 23)
    //      integer[4]              minute of the hour (0 to 59)
    //      integer[5]              second of the minute (0 to 59)
    //      integer[6]              time zone
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function parse ($args)
    {
        // set the default time properties
        $yr     = gmdate('Y');
        $mon    = gmdate('n');
        $day    = gmdate('j');
        $hr     = gmdate('G');
        $min    = gmdate('i');
        $sec    = gmdate('s');
        $tz     = 0;

        // if one argument is provided
        if (count($args) == 1 || count($args) == 2)
        {
            $date       = $args[0];
            $timeStamp  = $args[0];
            $sqlStamp   = $args[0];

            if(count($args) == 2 && ($args[0] instanceof Date || $args[1] instanceof Date))
            {
                $yr = $mon = $day = $hr = $min = $sec = $tz = 0;

                if(!is_null($args[0]))
                {
                    $yr         = $args[0]->getYear();
                    $mon        = $args[0]->getMonth();
                    $day        = $args[0]->getDay();
                }

                if(!is_null($args[1]))
                {
                    $hr  = $args[1]->getHour();
                    $min  = $args[1]->getMinute();
                    $sec  = $args[1]->getSecond();
                    $tz   = $args[1]->getTimeZone();
                }
            }
            // if the first argument is null, but
            else if(is_null($date) && count($args) == 2)
            {
                // use the current time in the correct time zone
                $min        += $args[1] * 60;
            }
            // if the argument is a Date object
            else if ($date instanceof Date)
            {
                // get the date properties
                $yr         = $date->getYear();
                $mon        = $date->getMonth();
                $day        = $date->getDay();
                $hr         = $date->getHour();
                $min        = $date->getMinute();
                $sec        = $date->getSecond();
                $tz   = $date->getTimeZone();
            }
            // if the argument is an array
            else if (is_array($date))
            {
                list($yr, $mon, $day, $hr, $min, $sec, $tz) = $date;
            }
            // if the argument is a SQL DATETIME
            else if (preg_match(
                '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $sqlStamp))
            {
                // get the date properties
                list($date, $time)      = explode(' ', $sqlStamp);
                list($yr, $mon, $day)   = explode('-', $date);
                list($hr, $min, $sec)   = explode(':', $time);
            }
            // if the argument is a SQL DATE
            else if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $sqlStamp))
            {
                // get the date properties
                list($yr, $mon, $day)   = explode('-', $sqlStamp);
                $hr = $min = $sec = 0;
            }
            // if the argument is a SQL TIME
            else if (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $sqlStamp))
            {
                // get the date properties
                list($hr, $min, $sec)   = explode(':', $sqlStamp);
                $yr  = 1970;
                $mon = 1;
                $day = 1;
            }
            // if the argument is a time stamp
            else
            {
                if(!(is_numeric($timeStamp) && $timeStamp > 0))
                {
                    $timeStamp = strtotime($date);
                }

                // get the date properties
                $yr     = gmdate('Y', $timeStamp);
                $mon    = gmdate('n', $timeStamp);
                $day    = gmdate('j', $timeStamp);
                $hr     = gmdate('G', $timeStamp);
                $min    = gmdate('i', $timeStamp);
                $sec    = gmdate('s', $timeStamp);
            }

            // if a time zone is provided, use that time zone
            if (count($args) == 2 && (!($args[1] instanceof Date) && !is_null($args[1])))
            {
                $tz     = $args[1];
            }
        }
        // if three arguments (year, month, and day) are provided
        else if (count($args) == 3 || count($args) == 4)
        {
            // get the date properties
            list($yr, $mon, $day)   = $args;

            if(is_null($yr))    $yr = gmdate('Y');
            if(is_null($mon))   $mon = gmdate('n');
            if(is_null($day))   $day = gmdate('j');

            $hr = $min = $sec = 0;

            if(count($args) == 4)
                $tz     = $args[3];
        }
        // if six arguments (hr, mon, day, hr, min, and sec) are provided
        else if (count($args) == 6 || count($args) == 7)
        {
            // get the date properties
            list($yr, $mon, $day, $hr, $min, $sec)  = $args;

            // if any are NULL
            if(is_null($yr))    $yr = gmdate('Y');
            if(is_null($mon))   $mon = gmdate('n');
            if(is_null($day))   $day = gmdate('j');
            if(is_null($hr))    $hr = gmdate('G');
            if(is_null($min))   $min = gmdate('i');
            if(is_null($sec))   $sec = gmdate('s');

            // if a time zone is provided, use that time zone
            if (count($args) == 7)
            {
                $tz     = $args[6];
            }
        }

        // simplify the date until it is valid
        $toRet   = $this->simplify(intval($yr), intval($mon), intval($day),
            intval($hr), intval($min), intval($sec));
        $toRet[] = floatval($tz);

        //  return the date
        return $toRet;
    }
    // parse method


    //*********************************************************************************
    //  name:       changeYear
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the year by the provided number of years
    //*********************************************************************************
    //  @param
    //      integer yrs     number of years to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeYear ($yrs)
    {
        // add the years
        $this->yr += $yrs;

        return $this;
    }
    // changeYear method

    //*********************************************************************************
    //  name:       changeMonth
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the month by the provided number of months
    //*********************************************************************************
    //  @param
    //      integer mons    number of months to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeMonth ($mons)
    {
        // add the months
        $this->mon  += $mons;

        $this->simplifyInternal();

        return $this;
    }
    // changeMonth method

    //*********************************************************************************
    //  name:       changeDay
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the day by the provided number of days
    //*********************************************************************************
    //  @param
    //      integer num     number of days to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeDay ($days)
    {
        //  add the days
        $this->day  += $days;

        $this->simplifyInternal();

        return $this;
    }
    // changeDay method

    //*********************************************************************************
    //  name:       changeHour
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the hour by the provided number of hours
    //*********************************************************************************
    //  @param
    //      integer hrs     number of hours to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeHour ($hrs)
    {
        // add the hours
        $this->hr   += $hrs;

        $this->simplifyInternal();

        return $this;
    }
    // changeHour method

    //*********************************************************************************
    //  name:       changeMinute
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the minute by the provided number of minutes
    //*********************************************************************************
    //  @param
    //      integer mins    number of minutes to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeMinute ($mins)
    {
        // add the minutes
        $this->min  += $mins;

        $this->simplifyInternal();

        return $this;
    }
    // changeMinute method

    //*********************************************************************************
    //  name:       changeSecond
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  changes the second by the provided number of seconds
    //*********************************************************************************
    //  @param
    //      integer secs    number of seconds to add or subtract
    //  @return void
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function changeSecond ($secs)
    {
        // add the seconds
        $this->sec  += $secs;

        $this->simplifyInternal();

        return $this;
    }
    // changeSecond method


    //*********************************************************************************
    //  name:       add
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  adds one date to another
    //*********************************************************************************
    //  @param
    //      integer yrs     number of years to add
    //      integer mons    number of months to add
    //      integer days    number of days to add
    //      integer hrs     number of hours to add
    //      integer mins    number of minutes to add
    //      integer secs    number of seconds to add
    //  @return
    //      bool true
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function add ($yrs, $mons, $days, $hrs, $mins, $secs)
    {
        // add to the date
        $this->changeYear($yrs);
        $this->changeMonth($mons);
        $this->changeDay($days);
        $this->changeHour($hrs);
        $this->changeMinute($mins);
        $this->changeSecond($secs);

        return true;
    }
    // add method

    //*********************************************************************************
    //  name:       diff
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  subtracts one date from another
    //  NOTE: diff() takes any parameters that Date::parse takes
    //*********************************************************************************
    //  @param  SEE NOTE
    //  @return bool
    //      bool false      the date is invalid
    //  @return integer[6]
    //      integer[0]      year
    //      integer[1]      month of the year (1 to 12)
    //      integer[2]      day of the month (1 to 28, 29, 30, or 31)
    //      integer[3]      hour of the day (0 to 23)
    //      integer[4]      minute of the hour (0 to 59)
    //      integer[5]      second of the minute (0 to 59)
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function diff ()
    {
        /*
        // get the date properties
        list($yr, $mon, $day, $hr, $min, $sec, $tz) = $this->parse(func_get_args());

        // calculate the beginning date
        $beg = min(array($yr, $mon, $day, $hr, $min, $sec), array($this->yr,
            $this->mon, $this->day, $this->hr, $this->min, $this->sec));

        // calculate the ending date
        $end = max(array($yr, $mon, $day, $hr, $min, $sec), array($this->yr,
            $this->mon, $this->day, $this->hr, $this->min, $this->sec));

        // calculate the differences
        $diffYr  = $end[0] - $beg[0];
        $diffMon = $end[1] - $beg[1];
        $diffDay = $end[2] - $beg[2];
        $diffHr  = $end[3] - $beg[3];
        $diffMin = $end[4] - $beg[4];
        $diffSec = $end[5] - $beg[5];

        // correct any invalid differences
        list($diffYr, $diffMon, $diffDay, $diffHr, $diffMin, $diffSec)
            = $this->simplifyDiff($diffYr, $diffMon, $diffDay, $diffHr, $diffMin,
            $diffSec, $end[0], $end[1]);

        return array(array($diffYr, $diffMon, $diffDay, $diffHr, $diffMin,
            $diffSec), array());
        */
    }
    // diff method


    //*********************************************************************************
    //  name:       format
    //  type:       method
    //  version:    05.07.06
    //*********************************************************************************
    //  takes any format string that PHP's date() function takes and parses it
    //  NOTE: the following are NOT implemented:
    //      B = SWATCH Internet time
    //      c = ISO-8601 date
    //      I = 0 | 1 (1 for DST in effect, 0 otherwise)
    //      W = ISO-8601 week number of year
    //*********************************************************************************
    //  @param
    //      string  format      format the date should be parsed into
    //      integer timeZone    time zone to convert to before formatting
    //  @return string  date    formatted date
    //*********************************************************************************
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public function format ($format, $timeZone = null)
    {
        #if(is_null($timeZone) && !is_null(User::getActiveUser()))
        #    $timeZone = User::getActiveUser()->getTimeZone();

        $date = new Date($this);

        // set the number of days per month (used for code 'w': week of year)
        $dayWkMon = array(null, 0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5);
        // if the year is a leap year
        if ($date->getDaysInMon($this->yr, 2) == 29)
        {
            // change the day offset for months 1, 2
            $dayWkMon[1]    = 6;
            $dayWkMon[2]    = 2;
        }

        // otherwise
        if (!is_null($timeZone))
        {
            $timeZone = $timeZone - $this->tz;
            $date->changeMinute($timeZone * 60);
        }

        //  get the date properties
        $yr     = $date->yr;
        $mon    = $date->mon;
        $day    = $date->day;
        $hr     = $date->hr;
        $min    = $date->min;
        $sec    = $date->sec;
        $tz     = $date->tz;

        // a = am | pm
        $code['a']   = ($hr < 12) ? 'am' : 'pm';
        // A = AM | PM
        $code['A']   = ($hr < 12) ? 'AM' : 'PM';
        // d = two digit day of month
        $code['d']   = sprintf('%02d', $day);
        // w = numeric day of week (0 - 6 for Sun - Sat)
        $code['w']   = (3 - floor(floor($yr / 100) % 4)) * 2; // first day of century
        $code['w']  += ($yr % 100) + floor(($yr % 100) / 4);  // first day of year
        $code['w']  += $dayWkMon[$mon];                       // first day of month
        $code['w']   = ($code['w'] + $day) % 7;               // day of week
        // D = Sun | Mon | Tue | Wed | Thu | Fri | Sat
        $code['D']   = self::$dayOfWk[$code['w']];
        // F = January | February | March | April | May | June | July | August |
        //     September | October | November | December
        $code['F']   = self::$monOfYear[$mon];
        // g = hour in 12-hour format
        $code['g']   = $hr > 12 ? $hr - 12 : ($hr == 0 ? 12 : $hr);
        // G = hour in 24-hour format
        $code['G']   = $hr;
        // h = two digit hour in 12-hour format
        $code['h']   = sprintf('%02d', $code['g']);
        // H = two digit hour in 24-hour format
        $code['H']   = sprintf('%02d', $code['G']);
        // i = two digit minutes
        $code['i']   = sprintf('%02d', $min);
        // j = day of month
        $code['j']   = $day;
        // l = Sunday | Monday | Tuesday | Wednesday | Thursday | Friday |
        //     Saturday
        $code['l']   = self::$dayOfWeek[$code['w']];
        // L = 0 | 1 (1 for leap year, zero otherwise)
        $code['L']   = !($yr % 4) ? 1 : 0;
        // m = two digit month
        $code['m']   = $mon < 10 ? '0' . $mon : $mon;
        // M = Jan | Feb | Mar | Apr | May | Jun | Jul | Aug | Sep | Oct | Nov |
        //     Dec
        $code['M']   = self::$monOfYr[$mon];
        // n = month
        $code['n']   = $mon;
        // O = +/- xy00 (GMT difference; e.g. -0800)
        $code['O']   = $timeZone >= 0 ? '+' : '';
        $code['O']  .= sprintf('%04d', $timeZone * 100);
        #$code['O']   = ($code['O'] >= 0 ? '+' : '-') . (abs($code['O']) < 10 ? '0' : '') . abs($code['O']);
        #$code['O']  .= '00';
        // s = two digit seconds
        $code['s']   = sprintf('%02d', $sec);
        // S = st | nd | rd | th (suffix for the day)
        $code['S']   = self::getNumSuffix($day);
        // t = number of days in month (considers leap year)
        $code['t']   = self::getDaysInMon($yr, $mon);
        // T = time zone setting of this machine
        $code['T']   = date('T');
        // U = UNIX timestamp
        $gmDate = new Date($this);
        $gmDate->changeMinute(-$this->tz * 60);
        $gmYr     = $gmDate->yr;
        $gmMon    = $gmDate->mon;
        $gmDay    = $gmDate->day;
        $gmHr     = $gmDate->hr;
        $gmMin    = $gmDate->min;
        $gmSec    = $gmDate->sec;
        if ($yr >= 1970)
        {
            //
            $code['U'] = (($gmDay - 1) * 86400) + ($gmHr * 3600) + ($gmMin * 60) + ($gmSec);
            for ($cnt = 1; $cnt < $gmMon; $cnt++)
            {
                $code['U'] += $gmDate->getDaysInMon($gmYr, $cnt) * 86400;
            }
            for ($cnt = 1970; $cnt < $gmYr; $cnt++)
            {
                $code['U'] += $gmDate->getDaysInYr($cnt) * 86400;
            }
        }
        else
        {
            //
            $code['U']  = (($gmDay - $this->getDaysInMon($gmYr, $gmMon)) * 86400);
            $code['U'] += (($gmHr - 23) * 3600) + (($gmMin - 59) * 60) + ($gmSec - 60);
            for ($cnt = 12; $cnt > $gmMon; $cnt--)
            {
                $code['U'] -= $date->getDaysInMon($gmYr, $cnt) * 86400;
            }
            for ($cnt = 1969; $cnt > $gmYr; $cnt--)
            {
                $code['U'] -= $date->getDaysInYr($cnt) * 86400;
            }
        }
        // Y = four digit year
        $code['Y']   = sprintf('%04d', $yr);
        // y = two digit year
        $code['y']   = sprintf('%02d', $yr % 100);
        // z = day of the year (0 - 365)
        $code['z']   = 0;
        for ($count = 1; $count < $mon; $count++)
            $code['z']  += $date->getDaysInMon($yr, $count);
        $code['z']  += $day - 1;
        // Z = timezone offset in seconds
        $code['Z']   = $timeZone * 3600;
        // c = ISO-8601 date
        $code['c']   = sprintf('%04d-%02d-%02dT%02d:%02d:%02d%s%02d:%02d',
            $code['Y'], $code['m'], $code['d'], $code['H'], $code['i'],
            $code['s'], ($timeZone < 0 ? '-' : '+'), floor(abs($timeZone)),
            floor(abs($timeZone - floor($timeZone)) * 60));
        // r = RFC-2822 date
        $code['r']   = sprintf('%s, %02d %02d %04d %02d:%02d:%02d %s',
            $code['D'], $code['d'], $code['M'], $code['Y'], $code['H'],
            $code['i'], $code['s'], $code['O']);
        // q = SQL date
        $code['q']   = sprintf('%04d-%02d-%02d', $code['Y'], $code['m'],
            $code['d']);
        // Q = SQL time
        $code['Q']   = sprintf('%02d:%02d:%02d', $code['H'], $code['i'],
            $code['s']);
        // u = microseconds
        $code['u']   = date('u');

        $date        = '';
        $slshCnt     = 0;

        // Why are these native functions as slow as my custom (PHP-coded) parser?
        //$date = date('Y-m-d H:i:s', $code['U']);
        //$date = date($format, $code['U']);
        //$date = str_replace(array_keys($code), $code, $format);

        //*
        // format the date
        for ($count = 0, $max = strlen($format); $count < $max; $count++)
        {
            $char   = substr($format, $count, 1);

            // if the character is a slash, record it
            if ($char == '\\')
            {
                $slshCnt++;
            }
            // otherwise
            else
            {
                // if the last character was a slash
                if ($slshCnt > 0)
                {
                    // if there are an odd number of slashes
                    if ($slshCnt % 2 == 1)
                    {
                        // append the slashes and the character
                        $date .= str_repeat('\\', ceil($slshCnt / 2) - 1);
                        $date .= $char;
                    }
                    // otherwise
                    else
                    {
                        // append the slashes
                        $date .= str_repeat('\\', ceil($slshCnt / 2));

                        // if the character has a replacement, append the
                        // replacement
                        if (array_key_exists($char, $code))
                        {
                            $date   .= $code[$char];
                        }
                        // otherwise append the character
                        else
                        {
                            $date   .= $char;
                        }
                    }

                    // this character is not a slash
                    $slshCnt    = 0;
                }
                // otherwise
                else
                {
                    // if the character has a replacement, append the replacement
                    if (array_key_exists($char, $code))
                    {
                        $date   .= $code[$char];
                    }
                    // otherwise append the character
                    else
                    {
                        $date   .= $char;
                    }
                }
            }
        }
        //*/

        return $date;
    }
    // format method


    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    public static function now ($format = 'U', $timeZone = null)
    {
        $now = new Date();
        return $now->format($format, $timeZone);
    }
    // now method

}
// Date class

?>
