<?php


require_once PATH_LIB . 'com/mephex/event/Event.php';


class MXT_DataObjectSaveEvent extends MXT_Event
{
    protected $obj;


    public function __construct(MXT_DataObject $obj)
    {
        $this->obj = $obj;
    }


    public function getObject()
    {
        return $this->obj;
    }
}



?>
