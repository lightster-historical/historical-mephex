<?php


class MXT_TimeDuration
{
    protected $hour;
    protected $minute;
    protected $second;


    public function __construct($hour = 0, $minute = 0, $second = 0)
    {
        $this->hour = intval($hour);
        $this->minute = intval($minute);
        $this->second = intval($second);
    }


    public function getHour()
    {
        return $this->hour;
    }

    public function getMinute()
    {
        return $this->minute;
    }

    public function getSecond()
    {
        return $this->second;
    }


    public static function initFromDatabase($value)
    {
        if(trim($value) == '')
            return null;
        else
        {
            $values = explode(':', $value);

            $hour = 0;
            $minute = 0;
            $second = 0;

            if(array_key_exists(0, $values))
                $hour = $values[0];
            if(array_key_exists(1, $values))
                $minute = $values[1];
            if(array_key_exists(2, $values))
                $second = $values[2];

            return new MXT_TimeDuration($hour, $minute, $second);
        }
    }
}



?>
