<?php


require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/form/field/BooleanField.php';


class MXT_BooleanType extends MXT_DefaultType
{
    public function __construct($defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
    }


    public function parseValue($value)
    {
        if($value)
            return true;
        else
            return false;
    }


    public function encodeForDatabase($value)
    {
        if($value)
            return '1';
        else if(!is_null($value))
            return '0';
        else if($this->allowNullValue() && is_null($this->getDefaultValue()))
            return 'NULL';
        else if(!is_null($this->getDefaultValue()))
            return $this->getDefaultValue() ? '1' : '0';
        else
            return '0';
    }

    public function decodeFromDatabase($value)
    {
        if($value == '')
            return null;
        else if($value == '0')
            return false;
        return true;
    }


    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_BooleanField($field->getKeyname(), $this->getConstraint());
        return $formField;
    }
}


?>
