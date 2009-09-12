<?php


class UndefinedMethodException extends Exception
{
    public function __construct($className, $methodName)
    {
        parent::__construct();
        
        $this->message = 'Method \'' . $methodName
            . '\' cannot be found in class \'' . $className . '\''; 
    }
    
    public function __toString()
    {
        return $this->message;
    }
}


?>
