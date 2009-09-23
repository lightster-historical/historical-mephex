<?php


require_once PATH_LIB . 'com/mephex/core/Exception.php';


class ConnectionException extends MXT_Exception
{
    public function __construct($message)
    {
        parent::__construct();

        $this->message = $message;
    }

    public function __toString()
    {
        return $this->message;
    }
}


?>
