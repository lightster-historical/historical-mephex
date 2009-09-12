<?php


require_once PATH_LIB . 'com/mephex/data-object/type/DateTimeType.php';
require_once PATH_LIB . 'com/mephex/form/field/DateField.php';


class MXT_DateType extends MXT_DateTimeType
{
    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof Date)
        {
            $timezone = $context->getValueOrDefault('timezone', null);
            if(!is_null($timezone))
                return $value->format('q', $timezone);
            else
                return $value->format('q');
        }
        else
            return $value;
    }


    public function encodeForDatabase($value)
    {
        if($value instanceof Date)
            return $value->format("'q'", 0);
        else if($this->allowNullValue() && is_null($value))
            return null;

        return false;
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_DateField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }
}



?>
