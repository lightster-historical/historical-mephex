<?php


require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';


class MXT_NumericType extends MXT_DefaultType
{
    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        return $value;
    }


    public function encodeForDatabase($value)
    {
        if(trim($value) == '' && $this->allowNullValue())
            return null;

        return "'" . addslashes($value) . "'";
    }

    public function decodeFromDatabase($value)
    {
        if($value == '')
            return null;

        return floatval($value);
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_InputField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }
}


?>
