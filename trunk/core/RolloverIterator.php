<?php
 


class RolloverIterator
{
    protected $index;
    protected $values;
    
    
    public function __construct($values)
    {
        $this->values = array_values($values);
        $this->index = 0;
    }
    
    
    public function getValue()
    {
        $value = $this->values[$this->index];
        
        $this->index = ($this->index + 1) % count($this->values);
        
        return $value;
    }
    
    public function __toString()
    {
        return $this->getValue();
    }
}


?>
