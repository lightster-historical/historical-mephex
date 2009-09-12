<?php


require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';


class MXT_SubmitConstraint implements MXT_FieldConstraint
{
    public function __construct()
    {
    }


    public function getDefaultFormValue()
    {
        return false;
    }


    public function getMinCount()
    {
        return 0;
    }

    public function getMaxCount()
    {
        return 1;
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
