<?php


require_once PATH_LIB . 'com/mephex/core/UndefinedKeyException.php';


class ArrayUtility
{
    public static function getValueUsingKey(&$array, $key)
    {
        if(array_key_exists($key, $array))
            return $array[$key];
        else
            throw new MXT_UndefinedKeyException($array, $key);
    }

    public static function getValueUsingKeyArray(&$array, $keyArray)
    {
        if(!is_array($keyArray))
            $keyArray = array($keyArray);

        $key = shift($arrayKey);

        if(is_null($key))
            return $array;
        else if(array_key_exists($key, $array))
            return self::getValueUsingKeyArray($array, $keyArray);
        else
            throw new MXT_UndefinedKeyException($array, $key);
    }

    /*public static function setValueUsingKeyArray(&$array, $keyArray, $value)
    {
        if(!is_array($keyArray))
            $keyArray = array($keyArray);

        $key = shift($arrayKey);

        if(count($arrayKey) <= 0)
        {
            $array[$key] = $value;
        }
        else
        {
            if(!array_key_exists($key, $array))
                $array[$key] =

            self::setValueUsingKeyArray($array, $keyArray, $value);
        }
    }*/
}


?>
