<?php


class Query
{
    protected static $history = array();

    protected $id;
    protected $query;
    protected $time;
    protected $backtrace;


    public function __construct($query)
    {
        self::$history[] = $this;

        $this->id = count(self::$history);
        $this->query = $query;
        $this->time = 0;
        
        $this->backtrace = debug_backtrace();
    }

    public function getQuery()
    {
        #echo $this->query;
        return $this->query;
    }

    public function addTime($time)
    {
        $this->time = $time;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getDebugBacktrace()
    {
        return $this->backtrace;
    }
    
    public function __toString()
    {
        return $this->getQuery();
    }


    public static function getHistory()
    {
        return self::$history;
    }
}


?>
