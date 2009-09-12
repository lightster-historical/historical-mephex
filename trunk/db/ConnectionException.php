<?php


class ConnectionException extends Exception
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
