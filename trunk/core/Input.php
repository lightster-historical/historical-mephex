<?php


require_once PATH_LIB . 'com/mephex/core/InputParser.php';
require_once PATH_LIB . 'com/mephex/core/InputValidator.php';
require_once PATH_LIB . 'com/mephex/core/InvalidValueException.php';
require_once PATH_LIB . 'com/mephex/core/MissingInputException.php';


class Input
{
    protected $inputMagicQuotes;
    protected $inputFields; 
    protected $args;

    
    public function __construct($args)
    {
        $this->isMagicQuotes = get_magic_quotes_gpc();
        $this->parsedValues = array();
        
        if ($this->isMagicQuotes)
        {
            $this->args = self::removeSlashes($args);
        }
        else
        {
            $this->args = $args;
        }
    }
    
    public function set($name, InputParser $parse = null, InputValidator $validate = null)
    {
        if(array_key_exists($name, $this->args))
        {
            if(is_null($validate) || $validate->isValid($this->args[$name]))
            {
                if(!is_null($parse))
                {
                    $this->parsedValues[$name]
                        = $parse->parseValue($this->args[$name]);
                }
                else
                {
                    $this->parsedValues[$name] = $this->args[$name];
                }
                
                return true;
            }
        }
        else
        {
            $this->parsedValues[$name] = null;
        }
        
        return false;
    }
    
    public function get($name, InputParser $parse = null)
    {
        // if the input variable was set
        if(array_key_exists($name, $this->parsedValues))
        {
            if(!is_null($parse))
            {
                return $parse->parseValue($this->parsedValues[$name]);
            }
            else
            {
                return $this->parsedValues[$name];
            }
        }
        // otherwise
        else
        {
            throw new Exception("com.mephex.core.Input: key '" . $name . "' does not exist.");
            // key does not exist;
            return null;
        }
    }
    
    public function isValid($name, InputValidator $validate)
    {
        return $validate->isValid($this->args[$name]);
    }
    
    public function exists($name)
    {
        return (array_key_exists($name, $this->parsedValues));
    }
    
    public static function removeSlashes($values)
    {
        // if the argument is an array
        if(is_array($values))
        {
            // loop through the values
            foreach($values as $key => $val)
            {
                // remove the slashes from the value
                $values[$key] = self::removeSlashes($val);
            }
        }
        // otherwise, remove the slashes from the value
        else
        {
            $values = stripslashes($values);
        }
    
        return $values;
    }
}


?>
