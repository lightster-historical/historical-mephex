<?php


require_once PATH_LIB . 'com/mephex/core/UndefinedKeyException.php';
require_once PATH_LIB . 'com/mephex/filesystem/File.php';


class Utility
{
    public static function ifEmpty($value, $default)
    {
        if(!empty($value) && !($value === 0))
            return $value;
        else
            return $default;
    }


    public static function getValueUsingKey(&$array, $key)
    {
        if(array_key_exists($key, $array))
        {
            return $array[$key];
        }
        else
        {
            throw new MXT_UndefinedKeyException($array, $key);
        }
    }


    public static function verifyLibraryPath($path)
    {
        if(!defined('PATH_LIB'))
            return false;

        $first = substr($path, 0, 1);
        if(!($first == '.' || $first == '/'))
            $path = PATH_LIB . $path;

        $path = realpath($path);

        if(File::isChild(PATH_LIB, $path))
        {
            return $path;
        }

        return false;
    }
}


?>
