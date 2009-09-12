<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/AbstractDataField.php';


interface MXT_AbstractDataField
{
    public function getKeyname();

    public function getDefaultValue();

    public function isValueEmpty($value);
    public function isEmptyAllowed();

    public function getValue(MXT_DataObject $obj);
    public function setValue(MXT_DataObject $obj, $value);

    public function getDisplayValue(MXT_DefaultContext $context, $value);

    public function isStored();
    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder);
    public function predecodeValue($value, MXT_DO_AbstractDecoder $decoder);
    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder);

    public function getFormField();
    public function encodeValueForForm($value);
    public function decodeValueFromForm($value);

    public function encodeDefaultValue($value);
    public function getFieldCacheCode();
}



?>
