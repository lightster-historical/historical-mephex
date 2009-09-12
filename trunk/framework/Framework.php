<?php


require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';


class Framework
{
    public function __construct($className)
    {
        HttpResponder::run($className);
    }
}


?>
