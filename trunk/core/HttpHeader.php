<?php


class HttpHeader
{   
    private static $statusNames = array
    (
        404 => "File Not Found",
        405 => "Method Not Allowed",
        500 => "Forbidden"
    );
    
    public static function sendStatus($code)
    {
        if(array_key_exists($code, self::$statusNames))
        {
            if(substr(php_sapi_name(), 0, 3) == 'cgi')
            {
                header('Status: ' . $code . ' ' . self::$statusNames[$code]
                    , TRUE);
            }
            else
            {
                header('HTTP/1.1 ' . $code . ' ' . self::$statusNames[$code]);
            }
        }
    }
    
    public static function sendErrorStatus($code, HttpResponder $errorHandler)
    {
        self::sendStatus($code);
        $errorHandler->init(array('error' => $code));
        $errorHandler->get(array('error' => $code));
    }
    
    public static function forwardTo($url, $exit = true)
    {
        header('Location: ' . $url);     
        
        if($exit)
            exit;
    }
    
    public static function getStatusName($code)
    {
        return self::$statusNames[$code];
    }
}


?>
