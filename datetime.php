<?php
require_once(__DIR__ . "/stdlib.php");

class python_datetime
{
    var $datetime = null;
    var $timetuple = null;

    function __construct()
    {
        if (func_num_args() == 1) {
            $arg = func_get_args()[0];

            if (gettype($arg == "string")) {
                $this->datetime = strtotime($arg);
            } elseif (gettype($arg) == "int") {
                $this->datetime = $arg;
            }

        } else if (func_num_args() > 1) {
            $args = func_get_args();

            $this->datetime = mktime(
                $args[0], // hour
                $args[1],  // minute
                $args[2], // second
                $args[3], // month
                $args[4], // day
                $args[5], // year
                $args[6]  // timezone
            );

        } else {
            $this->datetime = time();
        }
    }

    function _tt() {
        list($year, $month, $day, $hour, $minute, $second, $tzinfo) = explode(':', date('Y:n:j:H:i:s:Z', $this->datetime));

        $this->timetuple = new dict([
            'year' => int($year),
            'month' => int($month),
            'day' => int($day),
            'hour' => int($hour),
            'minute' => int($minute),
            'second' => int($second),
            'tzinfo' => int($tzinfo)
        ]);
    }

    function __toString()
    {
        return strval($this->_tt());
    }

    function now()
    {
        return new datetime();
    }

    function str($format_string) {
        return date($format_string, $this->datetime);
    }

    function rfc() {
        return $this->str("r");
    }

    function isoformat()
    {
        return $this->str("c");
    }

    function sqlformat($showdate=true, $showtime=false)
    {
        $return_value = "";

        if ($showdate) {
            $return_value .= date("Y-m-d", $this->datetime);
        }

        if ($showtime) {
            $return_value .= date("H:i:s", $this->datetime);
        }

        return $return_value;
    }

    function timedelta()
    {
        if (func_num_args() == 1) {
            $args_array = func_get_args();
            $kwargs = new dict($args_array[0]);

            $weeks = $kwargs->get('weeks', 0);
            $days = $kwargs->get('days', 0);
            $hours = $kwargs->get('hours', 0);
            $minutes = $kwargs->get('minutes', 0);
            $seconds = $kwargs->get('seconds', 0);
        } else {
            $args = func_get_args();

            $days = $args[0];
            $seconds = $args[1];
            $minutes = $args[2];
            $hours = $args[3];
            $weeks = $args[4];
        }

        $seconds_delta = 1;
        $minutes_delta = $seconds_delta * 60;
        $hours_delta = $minutes_delta * 60;
        $days_delta = $hours_delta * 24;
        $weeks_delta = $days_delta * 7;

        $time_delta = ($seconds * $seconds_delta) + ($minutes * $minutes_delta) + ($hours * $hours_delta) +
            ($days * $days_delta) + ($weeks * $weeks_delta);

        return $time_delta;
    }
}
