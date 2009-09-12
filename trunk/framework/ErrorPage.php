<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/db/MySQL.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';
require_once PATH_LIB . 'com/mephex/user/User.php';


class ErrorPage extends HttpResponder implements InputParser, InputValidator
{    
    private static $instance;
    
    
    //*
    public function get($args)
    {        
        print_r($args);
    }
    //*/
    
    public function isValid($value)
    {
        return is_numeric($value);
    }
    
    public function parseValue($value)
    {
        return intval($value);
    }
}


?>
