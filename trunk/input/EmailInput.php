<?php



require_once PATH_LIB . 'com/mephex/core/InputValidator.php';



class EmailInput implements InputValidator
{
    private static $instance;
    
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new EmailInput();
        
        return self::$instance;
    }
    
    public function isValid($value)
    {
        $exp = '/^[a-zA-Z0-9_-]+([\.a-zA-Z0-9_-]+)?@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';

        if (preg_match($exp, $value))
            return true;
        else
            return false;
    }
}



?>
