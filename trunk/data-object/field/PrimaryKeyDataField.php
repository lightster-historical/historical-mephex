<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/IntegerDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/HiddenIdField.php';


class MXT_PrimaryKeyDataField extends MXT_IntegerDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        parent::__construct($dataClass, $keyname);
    }
    
    
    public function getFormField()
    {
        return new MXT_HiddenIdField($this->getKeyname());
    }
}



?>
