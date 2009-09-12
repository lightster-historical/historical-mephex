<?php


require_once PATH_LIB . 'com/mephex/data-object/io/AbstractDecoder.php';


class MXT_DO_DatabaseDecoder implements MXT_DO_AbstractDecoder
{
    public function decodeBoolean($value)
    {
        if($value == '')
            return null;
        else
        {
            return !($value == '0');
        }
    }

    public function decodeDateTime($value)
    {
        if($value == '')
            return null;
        else
            return new Date($value);
    }

    public function decodeDate($value)
    {
        if($value == '')
            return null;
        else
            return new Date($value);
    }

    public function decodeInteger($value)
    {
        return $value;
    }

    public function decodeFloat($value)
    {
        return $value;
    }

    public function decodeString($value)
    {
        return $value;
    }
}



?>
