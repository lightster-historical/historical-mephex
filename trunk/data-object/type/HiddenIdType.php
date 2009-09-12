<?php


require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/form/field/HiddenIdField.php';


class MXT_HiddenIdType implements MXT_DataType
{
    public function __construct()
    {
    }


    public function getDefaultValue()
    {
        return -1;
    }

    public function allowNullValue()
    {
        return false;
    }


    public function parseValue($value)
    {
        return $value;
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        return $value;
    }


    public function encodeForDatabase($value)
    {
        return intval($value);
    }

    public function decodeEarlyFromDatabase($value)
    {
        return $value;
    }

    public function decodeFromDatabase($value)
    {
        return $value;
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_HiddenIdField($field->getKeyname());
        return $formField;
    }

    public function encodeForForm($value)
    {
        return $value;
    }

    public function decodeFromForm($value)
    {
        return $value;
    }


    public function getCacheCode()
    {
        $className = get_class($this);
        return "new $className()";
    }
}


?>
