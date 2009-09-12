<?php


require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/MinMaxConstraint.php';


abstract class MXT_SetType implements MXT_DataType
{
    protected $defaultValue;
    protected $allowNull;


    public function __construct($defaultValue, $allowNull)
    {
        $this->defaultValue = $defaultValue;
        $this->allowNull = $allowNull;
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
        if(in_array($value, $this->getValues()))
            return $value;

        return null;
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        return $value;
    }


    public function encodeForDatabase($value)
    {
        if($value instanceof MXT_Pair)
            return "'" . addslashes($value->left) . "'";
        else if($this->allowNullValue())
            return 'NULL';

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


    public abstract function getPairs();
    public abstract function getValues();


    protected function getConstraint()
    {
        $minCount = $this->allowNullValue() ? 0 : 1;
        $constraint = new MXT_MinMaxConstraint($minCount, 1);
        return $constraint;
    }

    public function getFormField(MXT_DataField $field)
    {
        $keyname = $field->getKeyname();

        $formField = new MXT_SetField($keyname, $this->getConstraint(), $this->getPairs());
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
        $params = array
        (
            $this->allowNullValue() ? 'true' : 'false',
            "'" . $this->getDefaultValue() . "'"
        );

        return "new $className(" . implode(',', $params) . ")";
    }
}



?>
