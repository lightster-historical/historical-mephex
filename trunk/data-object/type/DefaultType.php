<?php


require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/MinMaxConstraint.php';


class MXT_DefaultType implements MXT_DataType
{
    protected $defaultValue;
    protected $allowNull;

    protected $constraint;


    public function __construct($defaultValue, $allowNull)
    {
        $this->defaultValue = $defaultValue;
        $this->allowNull = $allowNull;

        $minCount = $this->allowNullValue() ? 0 : 1;
        $this->constraint = new MXT_MinMaxConstraint($minCount, 1);
    }


    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function allowNullValue()
    {
        return $this->allowNull;
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
        if((is_null($value) || $value == '') && $this->allowNullValue())
            return null;
        else if(!is_null($value))
            return "'" . addslashes($value) . "'";

        return false;
    }

    public function decodeEarlyFromDatabase($value)
    {
        return $value;
    }

    public function decodeFromDatabase($value)
    {
        return $value;
    }


    public function getConstraint()
    {
        return $this->constraint;
    }

    public function setConstraint(MXT_FieldConstraint $constraint)
    {
        $this->constraint = $constraint;
    }

    public function getFormField(MXT_DataField $field)
    {
        $formField = new MXT_InputField($field->getKeyname(), $this->getConstraint());
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
        $defaultValue = $this->getDefaultValue();
        if(is_null($defaultValue))
            $defaultValue = 'null';
        else
            $defaultValue = "'" . addslashes($defaultValue) . "'";

        $className = get_class($this);
        $params = array
        (
            $defaultValue,
            $this->allowNullValue() ? 'true' : 'false'
        );

        return "new $className(" . implode(',', $params) . ")";
    }
}


?>
