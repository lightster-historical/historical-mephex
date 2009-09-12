<?php


require_once PATH_LIB . 'com/mephex/dev/Log.php';


class MXT_Debug
{
    protected static $logPassword = null;
    protected static $showLog = array();


    public static function logException($logName, Exception $ex)
    {
        if(!MXT_Log::isLogSet($logName))
            MXT_Log::setLog($logName, PATH_LOG . $logName);

        MXT_Log::logException($logName, $ex);

        self::output($logName, $ex);
    }

    public static function logDeprecation($old, $new = null, $logName = 'deprecated')
    {
        if(is_null($new))
            self::logException($logName, new Exception($old . ' is deprecated.'));
        else
            self::logException($logName, new Exception($old . ' is deprecated. Use ' . $new . ' instead.'));
    }

    public static function output($logName, $message, $title = null)
    {
        // show messages?
        if( true || (array_key_exists($logName, self::$showLog) && self::$showLog[$logName]))
        {
            if($message instanceof Exception)
            {
                echo '<pre>';
                print_r($message);
                echo '</pre>';
            }
            else
            {
                echo '<pre>';
                print_r($message);
                echo '</pre>';
            }
        }
    }

    public static function passthru($logName, $message)
    {
        // show messages?
        if(array_key_exists($logName, self::$showLog) && self::$showLog[$logName])
        {
            return $message;
        }

        return '';
    }



    public static function isLogVisible($logName)
    {
        if(array_key_exists($logName, self::$showLog))
            return self::$showLog[$logName];

        return false;
    }

    public static function setLogVisibility($logName, $visible)
    {
        self::$showLog[$logName] = $visible;
    }


    public static function setDebugPassword($password)
    {
        self::$logPassword = $password;
    }

    public static function setVisibilityUsingRequestVars()
    {
        if(!is_null(self::$logPassword)
            && array_key_exists('dpw', $_REQUEST)
            && self::$logPassword == $_REQUEST['dpw'])
        {
            $logs = explode(';', $_REQUEST['dlogs']);

            foreach($logs as $log)
            {
                self::setLogVisibility($log, true);
            }
        }
    }
}



?>
