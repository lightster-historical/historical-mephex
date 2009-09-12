<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';


class MXT_DefaultLoader
{
    protected $class;
    protected $idQueue;


    public function __construct(MXT_DataClass $class)
    {
        $this->class = $class;
        $this->idQueue = array();
    }


    public function getClass()
    {
        return $this->class;
    }


    public function queueObjectUsingId($id)
    {
        $this->idQueue[$id] = $id;
    }

    public function loadQueuedObjects()
    {
        $class = $this->getClass();
        $class->loadObjectsUsingIds($this->idQueue);

        $this->idQueue = array();
    }
}



?>
