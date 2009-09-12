<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/form/field/DateTimeField.php';


class MXT_DateTimeType extends MXT_DefaultType
{
    protected $timezone;


    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
        $this->defaultValue = is_null($defaultValue) ? null : new Date($defaultValue);
    }


    public function parseValue($value)
    {
        if($value instanceof Date)
            return $value;
        else
            return null;
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
        else if($this->allowNullValue() && is_null($value))
            return null;

        return false;
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
        $formField = new MXT_DateTimeField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }


    public function getCacheCode()
    {
        $className = get_class($this);
        $params = array
        (
            (is_null($this->getDefaultValue()) ? 'null' : 'new Date()'),
            $this->allowNullValue() ? 'true' : 'false'
        );
        print_r($params);

        return "new $className(" . implode(',', $params) . ")";
    }
}



?>
