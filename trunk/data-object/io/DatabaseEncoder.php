<?php


require_once PATH_LIB . 'com/mephex/data-object/io/AbstractEncoder.php';


class MXT_DO_DatabaseEncoder implements MXT_DO_AbstractEncoder
{
    public function encodeBoolean($value)
    {
        if(is_null($value))
            return null;
        else
        {
            return ($value ? '1' : '0');
        }
    }

    public function encodeDateTime($value)
    {
        if($value instanceof Date)
            return $value->format('\'q Q\'', 0);
        else
            return null;
    }

    public function encodeDate($value)
    {
        if($value instanceof Date)
            return $value->format('\'q\'', 0);
        else
            return null;
    }

    public function encodeInteger($value)
    {
        if($value == '' || is_null($value))
            return null;
        else
            return intval($value);
    }

    public function encodeFloat($value)
    {
        if($value == '' || is_null($value))
            return null;
        else
            return floatval($value);
    }

    public function encodeString($value)
    {
        return '\'' . addslashes($value) . '\'';
    }
}



?>
