<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DateTimeDataField.php';


class MXT_ModifiedTimeDataField extends MXT_DateTimeDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        parent::__construct($dataClass, $keyname);
    }


    public function getDefaultValue()
    {
        return null;
    }


    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $encoder->encodeDateTime(new Date());
    }


    public function getFormField()
    {
        return null;
    }


    public static function convertDataField(MXT_DefaultDataField $oldField)
    {
        $field = new MXT_ModifiedTimeDataField($oldField->getDataClass(), $oldField->getKeyname());
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());

        return $field;
    }
}



?>
