<?php


class QueryResult
{
    protected static $count = 0;
    
    protected $result;
    

    public function __construct($result)
    {
        self::$count++;
        $this->result = $result;
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    
    public static function getQueryCount()
    {
        return self::$count;
    }
}


?>
