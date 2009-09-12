<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/core/Range.php';


class DateRange extends MXT_Range
{
    public function __construct(Date $start = null, Date $end = null)
    {
        // if neither date is null and the start date is after the end date
        if(!is_null($start) && !is_null($end) && $end->compareTo($start) < 0)
        {
            // swap the dates
            $this->min = $end;
            $this->max = $start;
        }
        else
        {
            $this->min = $start;
            $this->max = $end;
        }
    }
}



?>
