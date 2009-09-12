<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/IntegerDataField.php';


class MXT_ForeignKeyDataField extends MXT_IntegerDataField
{
    protected $dataObjFieldKeyname;


    public function __construct(MXT_DataClass $dataClass, $keyname, $dataObjFieldKeyname)
    {
        parent::__construct($dataClass, $keyname);

        $this->dataObjFieldKeyname = $dataObjFieldKeyname;
    }


    public function getDataObjectFieldKeyname()
    {
        return $this->dataObjFieldKeyname;
    }


    public function predecodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        $this->getDataClass()->getField($this->getDataObjectFieldKeyname())->predecodeValue($value, $decoder);

        return parent::predecodeValue($value, $decoder);
    }


    public static function convertFromIntegerDataField(MXT_IntegerDataField $oldField, $dataObjFieldKeyname)
    {
        $field = new MXT_ForeignKeyDataField($oldField->getDataClass(), $oldField->getKeyname(), $dataObjFieldKeyname);
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());

        return $field;
    }


    protected function getCacheCodeParameters()
    {
        return array
        (
            '$this', // in the context of MXT_CacheableDataClass->initFields()
            "'" . $this->getKeyname() . "'",
            "'" . $this->getDataObjectFieldKeyname() . "'"
        );
    }
}



?>
