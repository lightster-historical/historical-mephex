<?php


/*
MXT_InstanceCache is hashmap that keeps track of objects using specified
keys.
*/


class MXT_InstanceCache
{
    protected $instances;


    public function __construct()
    {
        $this->instances = array();
    }


    public function add($key, $value)
    {
        if(!$this->containsKey($key))
        {
            $this->instances[$key] = $value;
            return true;
        }

        return false;
    }

    public function replace($key, $value)
    {
        $this->instances[$key] = $value;
    }

    public function set($key, $value)
    {
        // set should add the item to the end of the array,
        // so delete any item that uses that key
        if($this->containsKey($key))
        {
            unset($this->instances[$key]);
        }
        $this->replace($key, $value);
    }


    public function containsKey($key)
    {
        return array_key_exists($key, $this->instances);
    }


    public function get($key)
    {
        if($this->containsKey($key))
            return $this->instances[$key];

        return null;
    }

    public function getAll()
    {
        return $this->instances;
    }
}



?>
