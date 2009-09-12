<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractDecoder.php';


// added: 2009/03/15 21:11
class MXT_DataObject
{
    protected $dataClass;

    protected $values;
    protected $cachedValues;
    protected $encodedValues;

    protected $isLoaded;
    protected $uniqueIndex;

    protected $decoder;


    public function __construct(MXT_DataClass $dataClass)
    {
        $this->dataClass = $dataClass;

        $this->values = array();
        $this->cachedValues = array();
        $this->encodedValues = array();

        $this->isLoaded = false;
        $this->uniqueIndex = null;

        $this->decoder = null;
    }


    protected static function getUsingClassNameAndObjectId($className, $id)
    {
        $className = strtolower($className);
        if(class_exists($className))
        {
            $class = call_user_func(array($className, 'getSingleton'));
            return $class->getObjectUsingId($id);
        }

        return null;
    }

    protected static function getAllUsingClassName($className)
    {
        $className = strtolower($className);
        if(class_exists($className))
        {
            $class = call_user_func(array($className, 'getSingleton'));
            return $class->getAllObjects();
        }

        return null;
    }


    public function getDataClass()
    {
        return $this->dataClass;
    }

    public function getDecoder()
    {
        return $this->decoder;
    }


    public function isCreated()
    {
        return ($this->getId() > 0);
    }


    public function isLoaded()
    {
        return $this->isLoaded;
    }

    public function getUniqueIndex()
    {
        return $this->uniqueIndex;
    }

    public function setLoaded($isLoaded)
    {
        if(is_bool($isLoaded))
            $this->isLoaded = $isLoaded;
    }

    public function setUniqueIndex(array $uniqueIndex)
    {
        $this->uniqueIndex = $uniqueIndex;
    }


    public function saveUsingWriter(MXT_DO_AbstractWriter $writer, MXT_SaveListener $saveListener)
    {
        $class = $this->getDataClass();
        return $class->saveUsingWriter($this, $writer, $saveListener);
    }


    public function setValueUsingField(MXT_AbstractDataField $field, $value)
    {
        $keyname = $field->getKeyname();
        $this->values[$keyname] = $value;
        unset($this->encodedValues[$keyname]);
        unset($this->cachedValues[$keyname]);

        return true;
    }

    public function setValue($keyname, $value)
    {
        $class = $this->getDataClass();
        /*
        if($class->isField($keyname))
        {
        //*/
            $field = $class->getField($keyname);
            return $field->setValue($this, $value);
            //$dataType = $field->getDataType();
            //$this->values[$keyname] = $dataType->parseValue($value);
            //unset($this->encodedValues[$keyname]);
        /*
        }
        else
        {
            $this->values[$keyname] = $value;
        }
        //*/
    }


    public function initEncodedValues(array $values, MXT_DO_AbstractDecoder $decoder)
    {
        $class = $this->getDataClass();

        $fields = $class->getFields();
        foreach($values as $keyname => $value)
        {
            $this->encodedValues[$keyname] = $fields[$keyname]->predecodeValue($value, $decoder);
        }

        $this->decoder = $decoder;

        $this->values = array();
    }

    /*
    public function setEncodedValue($keyname, $value, $decoder)
    {
        $class = $this->getDataClass();
        $field = $class->getField($keyname);
        $value = $field->predecodeValue($value, $decoder);

        $this->encodedValues[$keyname] = $value;
        if(array_key_exists($keyname, $this->values))
            unset($this->values[$keyname]);
    }
    */


    public function isValueChanged($keyname)
    {
        if(array_key_exists($keyname, $this->values)
            && !array_key_exists($keyname, $this->encodedValues))
            return true;

        return false;
    }


    public function getValueUsingField(MXT_AbstractDataField $field)
    {
        $keyname = $field->getKeyname();
        if(!array_key_exists($keyname, $this->values))
        {
            $class = $this->getDataClass();

            //if(!$this->isLoaded())
            //    $class->initObjectUsingIdAsLoaded($this, $this->getId());

            $value = Utility::getValueUsingKey($this->encodedValues, $keyname);
            $decodedValue = $field->decodeValue($value, $this->getDecoder());
            $this->setValue($keyname, $decodedValue);
        }

        return Utility::getValueUsingKey($this->values, $keyname);
    }

    public function getValue($keyname)
    {
        $class = $this->getDataClass();

        //*
        if(array_key_exists($keyname, $this->cachedValues))
        {
            return $this->cachedValues[$keyname];
        }
        else 
        //*/
        if($class->isField($keyname))
        {
            $field = $class->getField($keyname);
            $value = $field->getValue($this);
            $this->cachedValues[$keyname] = $value;
            return $value;
        }
        else
        {
            return Utility::getValueUsingKey($this->values, $keyname);
        }
    }

    public function getEncodedValue($keyname)
    {
        echo '<pre>';
        debug_print_backtrace();
        die(__CLASS__.'.'.__FUNCTION__);
        if(array_key_exists($keyname, $this->encodedValues))
            return $this->encodedValues[$keyname];
        else
        {
            $class = $this->getDataClass();
            $field = $class->getField($keyname);
            return $field->encodeForDatabase(Utility::getValueUsingKey($this->values, $keyname));
        }
    }

    public function getValueEncodedForDatabase($keyname)
    {
        $class = $this->getDataClass();
        $field = $class->getField($keyname);

        $value = $this->getValue($keyname);

        return $field->encodeForDatabase($value);
    }


    public function getId()
    {
        $dataClass = $this->getDataClass();
        $idField = $dataClass->getIdField();

        return $this->getValue($idField->getKeyname());
    }

    public function setId($id)
    {
        $dataClass = $this->getDataClass();
        $idField = $dataClass->getIdField();

        $this->setValue($idField->getKeyname(), $id);
        
        return $this->setValue($idField->getKeyname(), $id);
    }



    public function __toString()
    {
        $dataClass = $this->getDataClass();
        $idField = $dataClass->getIdField();

        $className = get_class($this);
        $idKeyname = $idField->getKeyname();
        $id = $this->getId();

        return $className . '[' . $idKeyname . '=' . $id . ']';
    }

    public function getPairValue()
    {
        return $this->__toString();
    }
}



?>
