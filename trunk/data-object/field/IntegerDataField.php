<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';


class MXT_IntegerDataField extends MXT_DefaultDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        parent::__construct($dataClass, $keyname);
        parent::setDefaultValue(0);
    }


    public function setDefaultValue($value)
    {
        parent::setDefaultValue(intval($value));
    }


    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $encoder->encodeInteger($value);
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $decoder->decodeInteger($value);
    }
    
        
    public function getFormField()
    {
        return new MXT_InputField($this->getKeyname(), $this->getConstraint());
    }
    
    
    public function encodeDefaultValue($value)
    {
        return intval($value);
    }
}



?>
