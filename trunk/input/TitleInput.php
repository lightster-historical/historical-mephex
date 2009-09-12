<?php



require_once PATH_LIB . 'com/mephex/core/InputParser.php';
require_once PATH_LIB . 'com/mephex/core/InputValidator.php';



class TitleInput implements InputParser, InputValidator
{
    private static $instance;
    
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new TitleInput();
        
        return self::$instance;
    }
    
    
    public function parseValue($value)
    {
        return trim($value);
    }
    
    public function isValid($value)
    {
        return trim($value) != '';
    }
}



?>
