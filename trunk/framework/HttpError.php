<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/db/MySQL.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/framework/ErrorPage.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';
require_once PATH_LIB . 'com/mephex/user/User.php';


class HttpError
{    
    private static $instance = null;
    
    
    private function HttpError()
    {
    }
    
    
    public static function getInstance($args)
    {
        if(!(self::$instance instanceof HttpResponder))
        {
            self::$instance = new ErrorPage($args);
        }
        
        return self::$instance;
    }
    
    public static function initInstance(HttpResponder $init)
    {
        if(!(self::$instance instanceof HttpResponder))
        {
            self::$instance = $init;
        }
    }
}


?>
