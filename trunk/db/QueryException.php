<?php


class QueryException extends Exception
{
    public function __construct($message, $query)
    {
        parent::__construct();

        $this->message = $message;
        $this->query = $query;
    }

    public function __toString()
    {
        return $this->message
            . '<p class="mephex-sql">' . $this->query . '</p>';
    }

    public function getQuery()
    {
        return $this->query;
    }
}


?>
