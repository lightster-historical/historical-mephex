<?php


require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';


class MXT_RequiredConstraint implements MXT_FieldConstraint
{
    protected $maxCount;

    public function __construct($maxCount = 1)
    {
        $this->maxCount = max($maxCount, 0);
    }


    public function getDefaultFormValue()
    {
        return '';
    }


    public function getMinCount()
    {
        return 1;
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
            || (is_scalar($value) && trim($value) == '');
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
