<?php
class myMiniDate {
    private $myTimestamp;
    static private $dateComponent = array(
                                                                        'second' => 's',
                                                                        'minute' => 'i',
                                                                        'hour' => 'G',
                                                                        'day' => 'j',
                                                                        'month' => 'n',
                                                                        'year' => 'Y',
                                                                        'dow' => 'w',
                                                                        'timestamp' => 'U'
    );
    static private $weekday = array(
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        0 => 'sunday'
    );

    public function __construct($ts = NULL) { $this->myTimestamp = is_null($ts)?time():$ts; }

    public function __set($var, $value) {
        list($c['second'], $c['minute'], $c['hour'], $c['day'], $c['month'], $c['year'], $c['dow']) = explode(' ', date('s i G j n Y w', $this->myTimestamp));
        switch ($var) {
            case 'dow':
                $this->myTimestamp = strtotime(self::$weekday[$value], $this->myTimestamp);
                break;
            case 'timestamp':
                $this->myTimestamp = $value;
                break;
            default:
                $c[$var] = $value;
                $this->myTimestamp = mktime($c['hour'], $c['minute'], $c['second'], $c['month'], $c['day'], $c['year']);
                break;
        }
    }


    public function __get($var) {
        return date(self::$dateComponent[$var], $this->myTimestamp);
    }

    public function modify($how) { return $this->myTimestamp = strtotime($how, $this->myTimestamp); }
}


class CronParser
{

    private $bits = Array(); //exploded String like 0 1 * * *
    private $now = Array();	//Array of cron-style entries for time()
    private $lastRan; 		//Timestamp of last ran time.
    private $taken;
    private $debug;
    private $year;
    private $month;
    private $day;
    private $hour;
    private $minute;
    private $minutes_arr = array();	//minutes array based on cron string
    private $hours_arr = array();	//hours array based on cron string
    private $months_arr = array();	//months array based on cron string

    public function getLastRan()
    {
        return explode(",", strftime("%M,%H,%d,%m,%w,%Y", $this->lastRan)); //Get the values for now in a format we can use
    }

    public function getLastRanUnix()
    {
        return $this->lastRan;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function debug($str)
    {
        if (is_array($str))
        {
            $this->debug .= "\nArray: ";
            foreach($str as $k=>$v)
            {
                $this->debug .= "$k=>$v, ";
            }

        }
        else
        {
            $this->debug .= "\n$str";
        }
        //echo nl2br($this->debug);
    }

    /**
     * Assumes that value is not *, and creates an array of valid numbers that
     * the string represents.  Returns an array.
     */
    public function expand_ranges($str, $max, $min = 1)
    {

        if($str == '*') {
            for($i = $min; $i <= $max; $i++) {
                $ret[] = $i;
            }
        }
        else if (strstr($str,  ","))
        {
            $arParts = explode(',', $str);
            foreach ($arParts AS $part)
            {
                if (strstr($part, '-'))
                {
                    $arRange = explode('-', $part);
                    for ($i = $arRange[0]; $i <= $arRange[1]; $i++)
                    {
                        $ret[] = $i;
                    }
                }
                else
                {
                    $ret[] = $part;
                }
            }
        }
        elseif (strstr($str,  '-'))
        {
            $arRange = explode('-', $str);
            for ($i = $arRange[0]; $i <= $arRange[1]; $i++)
            {
                $ret[] = $i;
            }
        }
        elseif (strstr($str,  '/')) {
            $temp = explode('/', $str);
            if(empty($temp[0])) $temp[0] = '*';
            $ar = $this->expand_ranges($temp[0], $max, $min);
            $ret = array();
            $pas = intval($temp[1]);

            foreach($ar as $t) {
                if(is_int($t) && $t%$pas == 0) {
                    $ret[] = $t;
                }
            }
        }
        else
        {
            $ret[] = $str;
        }
        $ret = array_unique($ret);
        sort($ret);
        return $ret;
    }

    public function daysinmonth($month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    /**
     *  Calculate the last due time before this moment
     */
    public function calcLastRan($string)
    {

        $tstart = microtime();
        $this->debug = "";
        $this->lastRan = 0;
        $this->year = NULL;
        $this->month = NULL;
        $this->day = NULL;
        $this->hour = NULL;
        $this->minute = NULL;
        $this->hours_arr = array();
        $this->minutes_arr = array();
        $this->months_arr = array();

        $string = preg_replace('/[\s]{2,}/', ' ', $string);

        if (preg_match('/[^-,* \\d]/', $string) !== 0)
        {
            $this->debug("Cron String contains invalid character");
            return false;
        }

        $this->debug("<b>Working on cron schedule: $string</b>");
        $this->bits = @explode(" ", $string);

        if (count($this->bits) != 5)
        {
            $this->debug("Cron string is invalid. Too many or too little sections after explode");
            return false;
        }

        //put the current time into an array
        $t = strftime("%M,%H,%d,%m,%w,%Y", time());
        $this->now = explode(",", $t);

        $this->year = $this->now[5];

        $arMonths = $this->_getMonthsArray();

        do
        {
            $this->month = array_pop($arMonths);
        }
        while ($this->month > $this->now[3]);

        if ($this->month === NULL)
        {
            $this->year = $this->year - 1;
            $this->debug("Not due within this year. So checking the previous year " . $this->year);
            $arMonths = $this->_getMonthsArray();
            $this->_prevMonth($arMonths);
        }
        elseif ($this->month == $this->now[3]) //now Sep, month = array(7,9,12)
        {
            $this->debug("Cron is due this month, getting days array.");
            $arDays = $this->_getDaysArray($this->month, $this->year);

            do
            {
                $this->day = array_pop($arDays);
            }
            while ($this->day > $this->now[2]);

            if ($this->day === NULL)
            {
                $this->debug("Smallest day is even greater than today");
                $this->_prevMonth($arMonths);
            }
            elseif ($this->day == $this->now[2])
            {
                $this->debug("Due to run today");
                $arHours = $this->_getHoursArray();

                do
                {
                    $this->hour = array_pop($arHours);
                }
                while ($this->hour > $this->now[1]);

                if ($this->hour === NULL) // now =2, arHours = array(3,5,7)
                {
                    $this->debug("Not due this hour and some earlier hours, so go for previous day");
                    $this->_prevDay($arDays, $arMonths);
                }
                elseif ($this->hour < $this->now[1]) //now =2, arHours = array(1,3,5)
                {
                    $this->minute = $this->_getLastMinute();
                }
                else // now =2, arHours = array(1,2,5)
                {
                    $this->debug("Due this hour");
                    $arMinutes = $this->_getMinutesArray();
                    do
                    {
                        $this->minute = array_pop($arMinutes);
                    }
                    while ($this->minute > $this->now[0]);

                    if ($this->minute === NULL)
                    {
                        $this->debug("Not due this minute, so go for previous hour.");
                        $this->_prevHour($arHours, $arDays, $arMonths);
                    }
                    else
                    {
                        $this->debug("Due this very minute or some earlier minutes before this moment within this hour.");
                    }
                }
            }
            else
            {
                $this->debug("Cron was due on " . $this->day . " of this month");
                $this->hour = $this->_getLastHour();
                $this->minute = $this->_getLastMinute();
            }
        }
        else //now Sep, arrMonths=array(7, 10)
        {
            $this->debug("Cron was due before this month. Previous month is: " . $this->year . '-' . $this->month);
            $this->day = $this->_getLastDay($this->month, $this->year);
            if ($this->day === NULL)
            {
                //No scheduled date within this month. So we will try the previous month in the month array
                $this->_prevMonth($arMonths);
            }
            else
            {
                $this->hour = $this->_getLastHour();
                $this->minute = $this->_getLastMinute();
            }
        }

        $tend = microtime();
        $this->taken = $tend - $tstart;
        $this->debug("Parsing $string taken " . $this->taken . " seconds");

        //if the last due is beyond 1970
        if ($this->minute === NULL)
        {
            $this->debug("Error calculating last due time");
            return false;
        }
        else
        {
            $this->debug("LAST DUE: " . $this->hour . ":" . $this->minute . " on " . $this->day . "/" . $this->month . "/" . $this->year);
            $this->lastRan = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);
            return true;
        }
    }

    /**
     *  Calculate the last due time before this moment
     */
    public function calcNextRan($string)
    {$tstart = microtime();
        $this->debug = "";
        $this->lastRan = 0;
        $this->year = NULL;
        $this->month = NULL;
        $this->day = NULL;
        $this->hour = NULL;
        $this->minute = NULL;
        $this->hours_arr = array();
        $this->minutes_arr = array();
        $this->months_arr = array();

        $string = preg_replace('/[\s]{2,}/', ' ', $string);

        if (preg_match('/[^-,* \\da-z\/]/', $string) !== 0)
        {
            $this->debug("Cron String contains invalid character");
            return false;
        }

        $this->debug("<b>Working on cron schedule: $string</b>");
        $this->bits = @explode(" ", $string);

        if (count($this->bits) != 5)
        {
            $this->debug("Cron string is invalid. Too many or too little sections after explode");
            return false;
        }

        //put the current time into an array
        $t = strftime("%M,%H,%d,%m,%w,%Y", time());
        $this->now = explode(",", $t);

        $this->year = $this->now[5];

        $arMonths = array_reverse($this->_getMonthsArray());

        do
        {
            $this->month = array_pop($arMonths);
        }
        while (count($arMonths) > 0 && $this->month < $this->now[3]);

        if ($this->month === NULL)
        {
            $this->year = $this->year + 1;
            $this->debug("Not due within this year. So checking the next year " . $this->year);
            $arMonths = array_reverse($this->_getMonthsArray());
            $this->_nextMonth($arMonths);
        }
        elseif ($this->month == $this->now[3]) //now Sep, month = array(7,9,12)
        {
            $this->debug("Cron is due this month, getting days array.");
            $arDays = array_reverse($this->_getDaysArray($this->month, $this->year));

            do
            {
                $this->day = array_pop($arDays);
            }
            while (count($arDays) > 0 && $this->day < $this->now[2]);

            if ($this->day === NULL || $this->day < $this->now[2])
            {
                $this->debug("Cron was due on next month");
                $this->_nextMonth($arMonths);
            }
            elseif ($this->day == $this->now[2])
            {
                $this->debug("Due to run today");
                $arHours = array_reverse($this->_getHoursArray());

                do
                {
                    $this->hour = array_pop($arHours);
                }
                while (count($this->hour) > 0 && $this->hour < $this->now[1]);

                if ($this->hour === NULL) // now =2, arHours = array(3,5,7)
                {
                    $this->debug("L'horaire est déjà passé on va donc au jour suivant");
                    $this->_nextDay($arDays, $arMonths);
                }
                elseif ($this->hour < $this->now[1]) //now =2, arHours = array(1,3,5)
                {
                    $this->minute = $this->_getNextMinute();
                }
                else // now =2, arHours = array(1,2,5)
                {
                    $this->debug("Due this hour");
                    $arMinutes = array_reverse($this->_getMinutesArray());
                    do
                    {
                        $this->minute = array_pop($arMinutes);
                    }
                    while (count($this->minute) > 0 && $this->minute < $this->now[0]);

                    if ($this->minute === NULL)
                    {
                        $this->debug("Not due this minute, so go for next hour.");
                        $this->_nextHour($arHours, $arDays, $arMonths);
                    }
                    else if($this->minute < $this->now[0])
                    {
                        $this->debug("Schedule allready ran this hour.");
                        $this->_nextHour($arHours, $arDays, $arMonths);
                    }
                }
            }
            else
            {
                $this->debug("Cron was due on " . $this->day . " of this month");
                $this->hour = $this->_getNextHour();
                $this->minute = $this->_getNextMinute();
            }
        }
        else
        {
            $arMonths = array_reverse($this->_getMonthsArray());
            $this->_nextMonth($arMonths);
            $this->debug("Cron was due after this month. Next month is: " . $this->year . '-' . $this->month);
            $this->day = $this->_getNextDay($this->month, $this->year);
            if ($this->day === NULL)
            {
                //No scheduled date within this month. So we will try the previous month in the month array
                $this->_nextMonth($arMonths);
            }
            else
            {
                $this->hour = $this->_getNextHour();
                $this->minute = $this->_getNextMinute();
            }
        }

        $tend = microtime();
        $this->taken = $tend - $tstart;
        $this->debug("Parsing $string taken " . $this->taken . " seconds");

        //if the last due is beyond 1970
        if ($this->minute === NULL)
        {
            $this->debug("Error calculating last due time");
            return false;
        }
        else
        {
            $this->debug("NEXT DUE: " . $this->hour . ":" . $this->minute . " on " . $this->day . "/" . $this->month . "/" . $this->year);
            $this->lastRan = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);
            return true;
        }
    }

    //get the due time before current month
    private function _prevMonth($arMonths)
    {
        $this->month = array_pop($arMonths);
        if ($this->month === NULL)
        {
            $this->year = $this->year -1;
            if ($this->year <= 1970)
            {
                $this->debug("Can not calculate last due time. At least not before 1970..");
            }
            else
            {
                $this->debug("Have to go for previous year " . $this->year);
                $arMonths = $this->_getMonthsArray();
                $this->_prevMonth($arMonths);
            }
        }
        else
        {
            $this->debug("Getting the last day for previous month: " . $this->year . '-' . $this->month);
            $this->day = $this->_getLastDay($this->month, $this->year);

            if ($this->day === NULL)
            {
                //no available date schedule in this month
                $this->_prevMonth($arMonths);
            }
            else
            {
                $this->hour = $this->_getLastHour();
                $this->minute = $this->_getLastMinute();
            }
        }

    }

    //get the due time before current month
    private function _nextMonth($arMonths)
    {
        $this->month = array_pop($arMonths);

        $nextYear = ($this->month < $this->now[3]);

        if ($this->month === NULL || $nextYear)
        {
            $this->year = $this->year +1;
            $this->debug("Have to go for next year " . $this->year);
            $arMonths = array_reverse($this->_getMonthsArray());
            $this->month = array_pop($arMonths);

            $this->debug("Getting the first day for next month: " . $this->year . '-' . $this->month);
            $this->day = $this->_getNextDay($this->month, $this->year);


            $this->hour = $this->_getNextHour();
            $this->minute = $this->_getNextMinute();
        }
        else
        {
            $this->debug("Getting the first day for next month: " . $this->year . '-' . $this->month);
            $this->day = $this->_getNextDay($this->month, $this->year);

            if ($this->day === NULL)
            {
                //no available date schedule in this month
                $this->_nextMonth($arMonths);
            }
            else
            {
                $this->hour = $this->_getNextHour();
                $this->minute = $this->_getNextMinute();
            }
        }

    }

    //get the due time before current day
    private function _prevDay($arDays, $arMonths)
    {
        $this->debug("Go for the previous day");
        $this->day = array_pop($arDays);
        if ($this->day === NULL)
        {
            $this->debug("Have to go for previous month");
            $this->_prevMonth($arMonths);
        }
        else
        {
            $this->hour = $this->_getLastHour();
            $this->minute = $this->_getLastMinute();
        }
    }

    //get the due time before current day
    private function _nextDay($arDays, $arMonths)
    {
        $this->debug("Go for the next day");
        $this->day = array_pop($arDays);
        if ($this->day === NULL)
        {
            $this->debug("Have to go for next month");
            $this->_nextMonth($arMonths);
        }
        else
        {
            $this->hour = $this->_getNextHour();
            $this->minute = $this->_getNextMinute();
        }
    }

    //get the due time before current hour
    private function _prevHour($arHours, $arDays, $arMonths)
    {
        $this->debug("Going for previous hour");
        $this->hour = array_pop($arHours);
        if ($this->hour === NULL)
        {
            $this->debug("Have to go for previous day");
            $this->_prevDay($arDays, $arMonths);
        }
        else
        {
            $this->minute = $this->_getLastMinute();
        }
    }

    //get the due time before current hour
    private function _nextHour($arHours, $arDays, $arMonths)
    {
        $this->debug("Going for next hour");
        $this->hour = array_pop($arHours);
        if ($this->hour === NULL)
        {
            $this->debug("Have to go for next day");
            $this->_nextDay($arDays, $arMonths);
        }
        else
        {
            $this->minute = $this->_getNextMinute();
        }
    }

    //not used at the moment
    private function _getLastMonth()
    {
        $months = $this->_getMonthsArray();
        $month = array_pop($months);

        return $month;
    }

    //not used at the moment
    private function _getNextMonth()
    {
        $months = array_reverse($this->_getMonthsArray());
        $month = array_pop($months);

        return $month;
    }

    private function _getLastDay($month, $year)
    {
        //put the available days for that month into an array
        $days = $this->_getDaysArray($month, $year);
        $day = array_pop($days);

        return $day;
    }

    private function _getNextDay($month, $year)
    {
        //put the available days for that month into an array
        $days = array_reverse($this->_getDaysArray($month, $year));
        $day = array_pop($days);

        return $day;
    }

    private function _getLastHour()
    {
        $hours = $this->_getHoursArray();
        $hour = array_pop($hours);

        return $hour;
    }

    private function _getNextHour()
    {
        $hours = array_reverse($this->_getHoursArray());
        $hour = array_pop($hours);

        return $hour;
    }

    private function _getLastMinute()
    {
        $minutes = $this->_getMinutesArray();
        $minute = array_pop($minutes);

        return $minute;
    }

    private function _getNextMinute()
    {
        $minutes = array_reverse($this->_getMinutesArray());
        $minute = array_pop($minutes);

        return $minute;
    }

    //remove the out of range array elements. $arr should be sorted already and does not contain duplicates
    private function _sanitize ($arr, $low, $high)
    {
        $count = count($arr);
        for ($i = 0; $i <= ($count - 1); $i++)
        {
            if ($arr[$i] < $low)
            {
                $this->debug("Remove out of range element. {$arr[$i]} is outside $low - $high");
                unset($arr[$i]);
            }
            else
            {
                break;
            }
        }

        for ($i = ($count - 1); $i >= 0; $i--)
        {
            if ($arr[$i] > $high)
            {
                $this->debug("Remove out of range element. {$arr[$i]} is outside $low - $high");
                unset ($arr[$i]);
            }
            else
            {
                break;
            }
        }

        //re-assign keys
        sort($arr);
        return $arr;
    }

    //given a month/year, list all the days within that month fell into the week days list.
    private function _getDaysArray($month, $year = 0)
    {
        if ($year == 0)
        {
            $year = $this->year;
        }

        $days = array();

        //return everyday of the month if both bit[2] and bit[4] are '*'
        if ($this->bits[2] == '*' AND $this->bits[4] == '*')
        {
            $days = $this->getDays($month, $year);
        }
        else
        {
            //create an array for the weekdays
            if ($this->bits[4] == '*')
            {
                for ($i = 0; $i <= 6; $i++)
                {
                    $arWeekdays[] = $i;
                }
            }
            else
            {
                $arWeekdays = $this->expand_ranges($this->bits[4], 7);
                $arWeekdays = $this->_sanitize($arWeekdays, 0, 7);

                foreach($arWeekdays as $k=>$v) {
                    if($v == 'mon') $arWeekdays[$k] = 1;
                    if($v == 'tue') $arWeekdays[$k] = 2;
                    if($v == 'wed') $arWeekdays[$k] = 3;
                    if($v == 'thu') $arWeekdays[$k] = 4;
                    if($v == 'fri') $arWeekdays[$k] = 5;
                    if($v == 'sat') $arWeekdays[$k] = 6;
                    if($v == 'sun') $arWeekdays[$k] = 7;
                }

                //map 7 to 0, both represents Sunday. Array is sorted already!
                if (in_array(7, $arWeekdays))
                {
                    if (in_array(0, $arWeekdays))
                    {
                        array_pop($arWeekdays);
                    }
                    else
                    {
                        $tmp[] = 0;
                        array_pop($arWeekdays);
                        $arWeekdays = array_merge($tmp, $arWeekdays);
                    }
                }
            }
            $this->debug("Array for the weekdays");
            $this->debug($arWeekdays);

            if ($this->bits[2] == '*')
            {
                $daysmonth = $this->getDays($month, $year);
            }
            else
            {
                $daysmonth = $this->expand_ranges($this->bits[2], $this->daysinmonth($month, $year));
                // so that we do not end up with 31 of Feb
                $daysinmonth = $this->daysinmonth($month, $year);
                $daysmonth = $this->_sanitize($daysmonth, 1, $daysinmonth);
            }

            //Now match these days with weekdays
            foreach ($daysmonth AS $day)
            {
                $wkday = date('w', mktime(0, 0, 0, $month, $day, $year));
                if (in_array($wkday, $arWeekdays))
                {
                    $days[] = $day;
                }
            }
        }
        $this->debug("Days array matching weekdays for $year-$month");
        $this->debug($days);
        return $days;
    }

    //given a month/year, return an array containing all the days in that month
    public function getDays($month, $year)
    {
        $daysinmonth = $this->daysinmonth($month, $year);
        $this->debug("Number of days in $year-$month : $daysinmonth");
        $days = array();
        for ($i = 1; $i <= $daysinmonth; $i++)
        {
            $days[] = $i;
        }
        return $days;
    }

    private function _getHoursArray()
    {
        if (empty($this->hours_arr))
        {
            $hours = array();

            if ($this->bits[1] == '*')
            {
                for ($i = 0; $i <= 23; $i++)
                {
                    $hours[] = $i;
                }
            }
            else
            {
                $hours = $this->expand_ranges($this->bits[1], 23);
                $hours = $this->_sanitize($hours, 0, 23);
            }

            $this->debug("Hour array");
            $this->debug($hours);
            $this->hours_arr = $hours;
        }
        return $this->hours_arr;
    }

    private function _getMinutesArray()
    {
        if (empty($this->minutes_arr))
        {
            $minutes = array();

            if ($this->bits[0] == '*')
            {
                for ($i = 0; $i <= 60; $i++)
                {
                    $minutes[] = $i;
                }
            }
            else
            {
                $minutes = $this->expand_ranges($this->bits[0], 59);
                $minutes = $this->_sanitize($minutes, 0, 59);
            }
            $this->debug("Minutes array");
            $this->debug($minutes);
            $this->minutes_arr = $minutes;
        }
        return $this->minutes_arr;
    }

    private function _getMonthsArray()
    {
        if (empty($this->months_arr))
        {
            $months = array();
            if ($this->bits[3] == '*')
            {
                for ($i = 1; $i <= 12; $i++)
                {
                    $months[] = $i;
                }
            }
            else
            {
                $months = $this->expand_ranges($this->bits[3], 12);
                $months = $this->_sanitize($months, 1, 12);
            }
            $this->debug("Months array");
            $this->debug($months);
            $this->months_arr = $months;
        }
        return $this->months_arr;
    }

}
?>