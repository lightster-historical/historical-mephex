<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/DateField.php';


class MXT_DateDataField extends MXT_DefaultDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        parent::__construct($dataClass, $keyname);
    }


    public function getDefaultValue()
    {
        return new Date();
    }


    public function isValueEmpty($value)
    {
        return is_null($value);
    }


    public function setValue(MXT_DataObject $obj, $value)
    {
        if($value instanceof Date || is_null($value))
        {
            return parent::setValue($obj, $value);
        }

        throw new Exception();
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof Date)
        {
            return $value->format('q');
        }
        else
            return $value;
    }


    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $encoder->encodeDate($value);
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $decoder->decodeDate($value);
    }


    protected function getConstraint()
    {
        return new MXT_MinMaxConstraint(0, 1);
    }

    public function getFormField()
    {
        return new MXT_DateField($this->getKeyname(), $this->getConstraint());
    }



    public static function convertDataField(MXT_DefaultDataField $oldField)
    {
        $field = new MXT_DateDataField($oldField->getDataClass(), $oldField->getKeyname());
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());

        return $field;
    }
}



?>
