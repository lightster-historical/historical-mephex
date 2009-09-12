<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/data-object/DataField.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/form/field/TimeDurationField.php';


class MXT_TimeDurationType extends MXT_DefaultType
{
    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
    }


    public function parseValue($value)
    {
        if($value instanceof MXT_TimeDuration)
            return $value;
        else
            return null;
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof MXT_TimeDuration)
            return sprintf('%02d:%02d:%02d', $value->getHour()
                , $value->getMinute(), $value->getSecond());
        else
            return $value;
    }


    public function encodeForDatabase($value)
    {
        if($value instanceof MXT_TimeDuration)
            return sprintf("'%02d:%02d:%02d'", $value->getHour()
                , $value->getMinute(), $value->getSecond());
        else if($this->allowNullValue() && is_null($value))
            return null;

        return false;
    }

    public function earlyDecodeFromDatabase($value)
    {
        return $value;
    }

    public function decodeFromDatabase($value)
    {
        if($value == '')
            return null;
        else
            return MXT_TimeDuration::initFromDatabase($value);
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_TimeDurationField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }
}



?>
