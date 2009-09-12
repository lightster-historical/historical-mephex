<?php


require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';


class MXT_MinMaxConstraint implements MXT_FieldConstraint
{
    protected $minCount;
    protected $maxCount;


    public function __construct($minCount = 1, $maxCount = 1)
    {
        $this->minCount = $minCount;
        $this->maxCount = max($maxCount, 0);

        if($this->maxCount != 0)
            $this->minCount = min($this->minCount, $this->maxCount);
    }


    public function getDefaultFormValue()
    {
        return '';
    }


    public function getMinCount()
    {
        return $this->minCount;
    }

    public function getMaxCount()
    {
        return $this->maxCount;
    }

    public function getDefaultCount()
    {
        return 1;
    }


    public function isEmpty($value)
    {
        return is_null($value)
            || (is_array($value) && count($value) <= 0)
            || (is_scalar($value) && trim($value) == '' && $value != '0');
    }

    public function isValid($value)
    {
        return true;
    }

    public function parseValue($value)
    {
        return $value;
    }
}



?>
