<?php



require_once PATH_LIB . 'com/mephex/core/Function.php';



class Objects
{
    protected static $objs = array();
    
    
    public static function add($name, $obj)
    {
        if(!array_key_exists($name, self::$objs))
        {
            self::$objs[$name] = $obj; 
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public static function delete($name)
    {
        if (array_key_exists($name, self::$objs))
        {
            unset(self::$objs[$name]);
        }
    }
    
    public static function get($name)
    {
        if (array_key_exists($name, self::$objs))
        {
            return self::$objs[$name];
        }
        else
        {
            $temp = null;
            return $temp;
        }
    }
    
    public static function checkDependency($keyName, $type)
    {
        if (!array_key_exists($keyName, self::$objs))
        {
            trigger_error('<em>' . $keyName . '</em> is required.',
                E_USER_ERROR);
        }
        else if (!(self::$objs[$keyName] instanceof $type))
        {
            trigger_error('<em>' . $keyName . '</em> is not of type <em>' . 
                $type . '</em>.', E_USER_ERROR);
        }
        else
        {
            return self::$objs[$keyName];
        }
    }
}


?>
