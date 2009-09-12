<?php


require_once PATH_LIB . 'com/mephex/data-object/type/DateTimeType.php';
require_once PATH_LIB . 'com/mephex/form/field/TimeField.php';


class MXT_TimeType extends MXT_DateTimeType
{
    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof Date)
        {
            if($context instanceof MXT_LocaleOutputterContext)
                return $value->format('Q', $context->getTimezone());
            else
                return $value->format('Q');
        }
        else
            return $value;
    }


    public function encodeForDatabase($value)
    {
        if($value instanceof Date)
            return $value->format("'Q'", 0);
        else if($this->allowNullValue() && is_null($value))
            return null;

        return false;
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_TimeField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }
}



?>
