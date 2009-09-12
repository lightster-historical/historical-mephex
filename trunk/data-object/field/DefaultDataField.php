<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/AbstractDataField.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/DefaultConstraint.php';


class MXT_DefaultDataField implements MXT_AbstractDataField
{
    protected $dataClass;
    protected $keyname;
    protected $emptyIsAllowed;
    protected $constraint;

    protected $defaultValue;

    protected $length;


    public function __construct(MXT_DataClass $dataClass, $keyname)
    {
        $this->dataClass = $dataClass;
        $this->keyname = $keyname;
        $this->emptyIsAllowed = false;
        $this->constraint = null;

        $this->defaultValue = '';

        $this->length = null;
    }


    public function getDataClass()
    {
        return $this->dataClass;
    }


    public function getKeyname()
    {
        return $this->keyname;
    }


    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }


    public function isValueEmpty($value)
    {
        if(is_null($value) || $value == '')
            return true;
        else
            return false;
    }

    public function isEmptyAllowed()
    {
        return $this->emptyIsAllowed;
    }

    public function setAllowEmpty($yesOrNo)
    {
        $this->emptyIsAllowed = ($yesOrNo ? true : false);
    }


    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }


    public function getValue(MXT_DataObject $obj)
    {
        if($this->getDataClass()->isObjectInstanceOf($obj))
        {
            return $obj->getValueUsingField($this);
        }

        throw new Exception();
    }

    public function setValue(MXT_DataObject $obj, $value)
    {
        if($this->getDataClass()->isObjectInstanceOf($obj))
        {
            return $obj->setValueUsingField($this, $value);
        }

        throw new Exception();
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        return $value;
    }


    public function isStored()
    {
        return true;
    }

    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        return $encoder->encodeString($value);
    }

    public function predecodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $value;
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $decoder->decodeString($value);
    }


    protected function getConstraint()
    {
        if(is_null($this->constraint))
        {
            $minCount = $this->isEmptyAllowed() ? 0 : 1;
            $this->constraint = new MXT_DefaultConstraint($this->getDefaultValue(), $minCount, 1);
        }

        return $this->constraint;
    }

    public function getFormField()
    {
        return new MXT_InputField($this->getKeyname(), $this->getConstraint());
    }

    public function encodeValueForForm($value)
    {
        return $value;
    }

    public function decodeValueFromForm($value)
    {
        return $value;
    }


    public function encodeDefaultValue($value)
    {
        if(!is_object($value) && !is_array($value))
            return '\'' . addslashes($value) . '\'';

        return false;
    }

    protected function getCacheCodeParameters()
    {
        return array
        (
            '$this', // in the context of MXT_CacheableDataClass->initFields()
            "'" . $this->getKeyname() . "'"
        );
    }

    public function getFieldCacheCode()
    {
        $className = get_class($this);

        $params = $this->getCacheCodeParameters();

        $code  = '$field = new ' . $className . '(' . implode(',', $params) . ");\n";
        $code .= '$field->setAllowEmpty(' . ($this->isEmptyAllowed() ? 'true' : 'false') . ");\n";

        $defaultValue = $this->encodeDefaultValue($this->getDefaultValue());
        if($defaultValue.'' != '')
            $code .= '$field->setDefaultValue(' . $defaultValue . ");\n";

        $length = $this->getLength();
        if(!is_null($length))
            $code .= '$field->setLength(' . $length . ");\n";

        $code .= '$fields->replace($field);' . "\n";

        return $code;
    }
}



?>
