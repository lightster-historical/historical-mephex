<?php


error_reporting(E_ALL | E_STRICT);


if(defined('PATH_LIB'))
{
    require_once PATH_LIB . 'com/mephex/framework/Framework.php';


    define('START_TIME', microtime(true));


    // if output buffering is turned on
    /*if(false)
    while(ob_get_level() > 0)
    {
        ob_end_clean();
    }*/


    /*
     * Define the class name as follows:
     *  - Take the file name of the script that was requested by the user's browser
     *  - Remove the path and extension from the file name
     *
     * Create an instance of the Framework class using the defined class name.
     * Framework is in charge of what class/method combination should be called.
     */
    $className = basename($_SERVER['SCRIPT_FILENAME'], '.php') . 'Responder';
    $className = str_replace('-', '', $className);
    $run = new Framework($className);
}


?>
