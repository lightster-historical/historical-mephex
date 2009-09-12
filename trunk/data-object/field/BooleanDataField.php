<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/BooleanField.php';


class MXT_BooleanDataField extends MXT_DefaultDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        parent::__construct($dataClass, $keyname);
        parent::setDefaultValue(false);
    }


    public function setDefaultValue($value)
    {
        parent::setDefaultValue($value ? true : false);
    }


    public function setValue(MXT_DataObject $obj, $value)
    {
        if(is_bool($value) || is_null($value))
        {
            return parent::setValue($obj, $value);
        }

        throw new Exception();
    }


    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $encoder->encodeBoolean($value);
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $decoder->decodeBoolean($value);
    }


    public function getFormField()
    {
        return new MXT_BooleanField($this->getKeyname(), $this->getConstraint());
    }

    public function encodeValueForForm($value)
    {
        return ($value ? '1' : '0');
    }

    public function decodeValueFromForm($value)
    {
        return !($value == '0');
    }


    public function encodeDefaultValue($value)
    {
        return $value ? 'true' : 'false';
    }


    public static function convertDataField(MXT_DefaultDataField $oldField)
    {
        $field = new MXT_BooleanDataField($oldField->getDataClass(), $oldField->getKeyname());
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());

        return $field;
    }
}



?>
