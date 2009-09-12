<?php

/*
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
// added: 2009/03/15 20:44
class MXT_DataField extends MXT_DefaultDataField
{
    protected $keyname;
    protected $dataType;

    protected $formField;


    public function __construct($keyname, MXT_DataType $dataType)
    {
        $this->keyname = $keyname;
        $this->dataType = $dataType;

        $this->formField = null;
    }


    public function getKeyname()
    {
        return $this->keyname;
    }

    public function getDataType()
    {
        return $this->dataType;
    }


    public function isValueEmpty($value)
    {
        return true;
    }

    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $value;
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $value;
    }




    public function getDefaultValue()
    {
        $dataType = $this->getDataType();
        return $dataType->getDefaultValue();
    }

    public function allowNullValue()
    {
        $dataType = $this->getDataType();
        return $dataType->allowNullValue();
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        $dataType = $this->getDataType();
        return $dataType->getDisplayValue($context, $value);
    }


    public function encodeForDatabase($value)
    {
        $dataType = $this->getDataType();
        return $dataType->encodeForDatabase($value);
    }

    public function decodeEarlyFromDatabase($value)
    {
        $dataType = $this->getDataType();
        return $dataType->decodeEarlyFromDatabase($value);
    }

    public function decodeFromDatabase($value)
    {
        $dataType = $this->getDataType();
        return $dataType->decodeFromDatabase($value);
    }


    public function getFormField()
    {
        if(is_null($this->formField))
        {
            $dataType = $this->getDataType();
            $this->formField = $dataType->getFormField($this);
        }

        return $this->formField;
    }

    public function encodeForForm($value)
    {
        $dataType = $this->getDataType();
        return $dataType->encodeForForm($value);
    }

    public function decodeFromForm($value)
    {
        $dataType = $this->getDataType();
        return $dataType->decodeFromForm($value);
    }


    public function setDataType(MXT_DataType $dataType)
    {
        if($this->dataType != $dataType)
        {
            $this->formField = null;
            $this->dataType = $dataType;
        }
    }



    public function getCacheCode()
    {
        $className = get_class($this);
        $dataType = $this->getDataType();
        $params = array
        (
            "'" . $this->getKeyname() . "'",
            $dataType->getCacheCode()
        );

        return '$fields[\'' . $this->getKeyname() . "'] = new $className(" . implode(',', $params) . ");\n";
    }
}

*/

?>
