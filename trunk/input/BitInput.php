<?php



require_once PATH_LIB . 'com/mephex/core/InputParser.php';
require_once PATH_LIB . 'com/mephex/core/InputValidator.php';



class BitInput implements InputParser, InputValidator
{
    private static $instance;
    
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new BitInput();
        
        return self::$instance;
    }
    
    
    public function parseValue($value)
    {
        return (intval($value) != 0) ? '1' : '0';
    }
    
    public function isValid($value)
    {
        return true;
    }
}



?>
