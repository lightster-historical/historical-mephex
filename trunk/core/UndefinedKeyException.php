<?php


class MXT_UndefinedKeyException extends Exception
{
    protected $array;
    protected $key;


    public function __construct(&$array, $key)
    {
        parent::__construct("Array key '$key' not found.");

        $this->array = &$array;
        $this->key = $key;
    }


    public function &getArray()
    {
        return $this->array;
    }

    public function getKey()
    {
        return $this->key;
    }
}


?>
