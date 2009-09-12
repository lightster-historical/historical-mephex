<?php



require_once PATH_LIB . 'com/mephex/core/InputParser.php';
require_once PATH_LIB . 'com/mephex/core/InputValidator.php';



class IntegerInput implements InputParser, InputValidator
{
    private static $instance;
    
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new IntegerInput();
        
        return self::$instance;
    }
    
    
    public function parseValue($value)
    {
        if(is_array($value))
        {
            foreach($value as $key => $val)
            {
                if(!is_null($val)) 
                    $value[$key] = intval($val);
            }
        }
        else
        {
            if(!is_null($value))
                $value = intval($value);
        }
        
        return $value;
    }
    
    public function isValid($value)
    {
        if(is_array($value))
        {
            foreach($value as $val)
            {
                if(is_numeric($val))
                    return false;
            }
            
            return true;
        }
        else
        {
            return is_numeric($value);
        }
    }
}



?>
