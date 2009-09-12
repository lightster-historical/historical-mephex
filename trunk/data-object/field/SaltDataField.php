<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/util/StringUtil.php';


class MXT_SaltDataField extends MXT_DefaultDataField
{
    public function __construct(MXT_DataClass $dataClass, $keyname, $length)
    {
        parent::__construct($dataClass, $keyname);

        $this->setLength($length);
    }


    public function setLength($length)
    {
        $length = intval($length);
        if($length > 0)
        {
            parent::setLength($length);
        }
        else
        {
            throw new Exception();
        }
    }


    public function generateNewSalt(MXT_DataObject $obj)
    {
        $salt = MXT_StringUtil::generateRandomAlphaNumericString($length);

        $this->setValue($obj, $salt);
    }


    public function getFormField()
    {
        return null;
    }



    public static function convertFromDefaultDataField(MXT_DefaultDataField $oldField)
    {
        $field = new MXT_SaltDataField($oldField->getDataClass()
            , $oldField->getKeyname(), $oldField->getLength());
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());

        return $field;
    }
}



?>
