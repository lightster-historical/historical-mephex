<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/list/AbstractList.php';


class MXT_DO_DefaultList extends MXT_DO_AbstractList
{
    protected $class;
    protected $itemsPerPage;


    public function __construct(MXT_DataClass $class, $itemsPerPage = 0)
    {
        $this->class = $class;
        $this->itemsPerPage = $itemsPerPage;

        parent::__construct();
    }


    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }


    public function getDataClass()
    {
        return $this->class;
    }
}



?>
