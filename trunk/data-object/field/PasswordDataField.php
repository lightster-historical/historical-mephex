<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/DefaultConstraint.php';


class MXT_PasswordDataField extends MXT_DefaultDataField
{
    protected $saltField;
    protected $dateTimeField;


    public function __construct(MXT_DataClass $dataClass, $keyname
        , MXT_SaltDataField $saltField = null
        , MXT_DateTimeDataField $dateTimeField = null)
    {
        parent::__construct($dataClass, $keyname);

        $this->saltField = $saltField;
        $this->dateTimeField = $dateTimeField;

        if(!is_null($saltField))
        {
            #$saltField->addChangeListener($this);
        }
    }


    public function getSaltField()
    {
        return $this->saltField;
    }

    public function getDateTimeField()
    {
        return $this->dateTimeField;
    }


    public function setValue(MXT_DataObject $obj, $value)
    {
        if($this->getDataClass()->isObjectInstanceOf($obj))
        {
            $saltField = $this->getSaltField();
            $dateTimeField = $this->getDateTimeField();

            if(!is_null($saltField))
            {
                $saltField->generateNewSalt($obj);
                $password = md5($value . $saltField->getValue());
            }
            else
            {
                $password = md5($value);
            }

            if(!is_null($dateTimeField))
            {
                $dateTimeField->setValue($obj, new Date());
            }

            return $obj->setValueUsingField($this, $password);
        }

        throw new Exception();
    }


    public function getFormField()
    {
        return new MXT_PasswordField($this->getKeyname(), $this->getConstraint());
    }


    protected function getCacheCodeParameters()
    {
        $params = parent::getCacheCodeParameters();

        $saltField = $this->getSaltField();
        $dateTimeField = $this->getDateTimeField();

        if(!is_null($saltField))
            $params = '$fields->get(' . $saltField->getKeyname() . ')';
        else
            $params = 'null';

        if(!is_null($dateTimeField))
            $params = '$fields->get(' . $dateTimeField->getKeyname() . ')';
        else
            $params = 'null';
    }



    public static function convertFromDefaultDataField(
        MXT_DefaultDataField $oldField, MXT_SaltDataField $saltField
        , MXT_DateTimeDataField $dateTimeField)
    {
        $field = new MXT_PasswordDataField($field->getDataClass()
            , $oldField->getKeyname(), $saltField, $dateTimeField);
        $field->setAllowEmpty($oldField->isEmptyAllowed());
        $field->setDefaultValue($oldField->getDefaultValue());
        $field->setLength($oldField->getLength());

        return $field;
    }
}



?>
