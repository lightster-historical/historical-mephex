<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/form/AbstractForm.php';
require_once PATH_LIB . 'com/mephex/event/SaveListener.php';


abstract class MXT_DO_AbstractDefaultForm extends MXT_DO_AbstractForm
{
    protected $saveListener;


    public function __construct(MXT_SaveListener $saveListener
        , $action)
    {
        parent::__construct($action);

        $this->saveListener = $saveListener;
    }


    public function getSaveListener()
    {
        return $this->saveListener;
    }
}



?>
