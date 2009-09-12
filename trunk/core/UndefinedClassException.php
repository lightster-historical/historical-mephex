<?php


class UndefinedClassException extends Exception
{
    public function __construct($className)
    {
        parent::__construct();
        
        $this->message = 'Class \'' . $className 
            . '\' cannot be found'; 
    }
    
    public function __toString()
    {
        return $this->message;
    }
}


?>
