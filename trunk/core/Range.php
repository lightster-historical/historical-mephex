<?php


class MXT_Range
{
    protected $min;
    protected $max;


    public function __construct($min, $max)
    {
        $this->min = min($min, $max);
        $this->max = max($min, $max);
    }


    public function getMin()
    {
        return $this->min;
    }

    public function getStart()
    {
        return $this->getMin();
    }

    public function getMax()
    {
        return $this->max;
    }

    public function getEnd()
    {
        return $this->getMin();
    }
}


?>
