<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/AbstractFilterable.php';
require_once PATH_LIB . 'com/mephex/form/field/SetField.php';


class MXT_ForeignObjectDataField extends MXT_DefaultDataField implements MXT_DO_AbstractFilterable
{
    protected $foreignDataClass;
    protected $foreignKeyFieldName;

    protected $pairs;


    public function __construct(MXT_DataClass $dataClass, $keyname, $foreignKeyname
        , $foreignClass)
    {
        parent::__construct($dataClass, $keyname);

        $this->foreignDataClass = $foreignClass;
        $this->foreignKeyFieldName = $foreignKeyname;

        $this->pairs = null;
    }


    public function getForeignDataClass()
    {
        if(!($this->foreignDataClass instanceof MXT_DataClass))
            $this->foreignDataClass = MXT_DataClass::getSingletonUsingClassName($this->foreignDataClass);
            
        return $this->foreignDataClass;
    }

    public function getForeignKeyFieldName()
    {
        return $this->foreignKeyFieldName;
    }

    public function getForeignKeyField()
    {
        return $this->getDataClass()->getField($this->getForeignKeyFieldName());
    }


    public function getDefaultValue()
    {
        return null;
    }


    public function isValueEmpty($value)
    {
        return !($this->getForeignDataClass()->isObjectInstanceof($value));
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof MXT_DataObject)
            return $value->getPairValue();

        return '';
    }


    public function getValue(MXT_DataObject $obj)
    {
        if($this->getDataClass()->isObjectInstanceOf($obj))
        {
            $foreignField = $this->getForeignKeyFieldName();
            $foreignClass = $this->getForeignDataClass();
            $foreignObj = $foreignClass->getObjectUsingId($obj->getValue($foreignField));
            return $foreignObj;
        }

        throw new Exception();
    }

    public function setValue(MXT_DataObject $obj, $value)
    {
        if($this->getDataClass()->isObjectInstanceOf($obj))
        {
            if($this->getForeignDataClass()->isObjectInstanceOf($value))
            {
                $foreignField = $this->getForeignKeyFieldName();
                return $obj->setValue($foreignField, $value->getId());
            }
            else if(is_null($value))
            {
                return null;
            }
            
            throw new Exception(2);
        }

        throw new Exception(1);
    }


    public function isStored()
    {
        return false;
    }
    
    public function encodeValue($value, MXT_DO_AbstractEncoder $encoder)
    {
        if($value instanceof MXT_DataObject)
        {
            $id = $encoder->encodeInteger($value->getId());
            return $id;
        }
        
        return null;
    }

    public function predecodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        $id = $decoder->decodeInteger($value);
        $this->getForeignDataClass()->queueObjectUsingId($id);

        return $id;
    }

    public function decodeValue($value, MXT_DO_AbstractDecoder $decoder)
    {
        return $this->getForeignDataClass()->getObjectUsingId($value);
    }





    public function getPairKey(MXT_DataObject $value)
    {
        $class = $this->getForeignDataClass();
        $idField = $class->getIdField();

        return $value->getValue($idField->getKeyname());
    }

    public function getPairValue(MXT_DataObject $value)
    {
        return $value->getPairValue();
    }

    public function initPairs()
    {
        $class = $this->getForeignDataClass();

        if($this->isEmptyAllowed())
        {
            $this->pairs = array(null);
            $this->values = array(null);
        }

        $allObjects = $class->getAllObjects();
        foreach($allObjects as $obj)
        {
            $value = $this->getPairValue($obj);

            $this->pairs[] = new MXT_Pair($this->getPairKey($obj), $value);
            $this->values[] = $value;
        }
    }

    public function getPairs()
    {
        if(is_null($this->pairs))
            $this->initPairs();

        return $this->pairs;
    }

    public function getValues()
    {
        if(is_null($this->values))
            $this->initPairs();

        return $this->values;
    }



    public function getFormField()
    {
        $formField = new MXT_SetField
        (
            $this->getKeyname(),
            $this->getConstraint(),
            $this->getPairs()
        );
        return $formField;
    }

    public function encodeValueForForm($value)
    {
        $class = $this->getForeignDataClass();
        if($value instanceof MXT_DataObject
            && $value->getDataClass() instanceof $class)
        {
            return $value->getId();
        }

        return null;
    }

    public function decodeValueFromForm($value)
    {
        if($value instanceof MXT_Pair)
        {
            $class = $this->getForeignDataClass();
            $obj = $class->getObjectUsingId($value->left);
            return $obj;
        }
        else if(is_numeric($value))
        {
            $class = $this->getForeignDataClass();
            $obj = $class->getObjectUsingId($value);
            return $obj;
        }

        return null;
    }


    protected function getCacheCodeParameters()
    {
        return array
        (
            '$this', // in the context of MXT_CacheableDataClass->initFields()
            "'" . $this->getKeyname() . "'",
            "'" . $this->getForeignKeyFieldName() . "'",
            "'" . get_class($this->getForeignDataClass()) . "'"
        );
    }
    
    
    
    public function getFilterableFieldKeyname()
    {
        return $this->getKeyname().'Id';
    }
}



?>
