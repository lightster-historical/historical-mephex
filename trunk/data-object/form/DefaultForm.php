<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/form/AbstractDefaultForm.php';
require_once PATH_LIB . 'com/mephex/event/SaveListener.php';


class MXT_DO_DefaultForm extends MXT_DO_AbstractDefaultForm
{
    protected $obj;
    protected $class;


    public function __construct(MXT_DataObject $obj, MXT_SaveListener $saveListener
        , $action)
    {
        $this->obj = $obj;
        $this->class = $obj->getDataClass();
        parent::__construct($saveListener, $action);
    }


    public function getDataObject()
    {
        return $this->obj;
    }

    public function getDataCLass()
    {
        return $this->class;
    }
}



?>
