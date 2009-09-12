<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/form/field/TimeField.php';


class MXT_CreationTimeType implements MXT_DataType
{
    public function __construct()
    {
    }


    public function getDefaultValue()
    {
        $now = new Date();
        return $now;
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
        if($value instanceof Date)
        {
            $timezone = $context->getValueOrDefault('timezone', null);
            if(!is_null($timezone))
                return $value->format('q Q', $timezone);
            else
                return $value->format('q Q');
        }
        else
            return $value;
    }


    public function encodeForDatabase($value)
    {
        if($value instanceof Date)
            return $value->format("'q Q'", 0);

        return false;
    }

    public function decodeEarlyFromDatabase($value)
    {
        return $value;
    }

    public function decodeFromDatabase($value)
    {
        if($value == '')
            return null;
        else
            return new Date($value);
    }


    public function getFormField(MXT_DataField $field)
    {
        return null;
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
