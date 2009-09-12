<?php


require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/DataType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/SetType.php';


class MXT_DataObjectType extends MXT_SetType
{
    protected $dataClass;

    protected $pairs;


    public function __construct(MXT_DataClass $dataClass, $defaultValue, $allowNull)
    {
        parent::__construct($defaultValue, $allowNull);
        $this->dataClass = $dataClass;
        $this->pairs = null;
    }


    public function getDataClass()
    {
        return $this->dataClass;
    }

    public function parseValue($value)
    {
        $dataClass = $this->getDataClass();
        $objType = $dataClass->getDataObjectName();
        if($value instanceof $objType)
            return $value;

        return null;
    }


    public function getDisplayValue(MXT_DefaultContext $context, $value)
    {
        if($value instanceof MXT_DataObject)
            return $value->getPairValue();
        else
            return $value;
    }


    public function getPairKey(MXT_DataObject $value)
    {
        $class = $value->getDataClass();
        $idField = $class->getIdField();

        return $value->getValue($idField->getKeyname());
    }

    public function getPairValue(MXT_DataObject $value)
    {
        return $value->getPairValue();
    }

    public function initPairs()
    {
        $class = $this->getDataClass();

        if($this->allowNullValue())
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


    public function encodeForDatabase($value)
    {
        $class = $this->getDataClass();
        if($value instanceof MXT_DataObject
            && $value->getDataClass() instanceof $class)
        {
            $idField = $class->getIdField();
            return $value->getValueEncodedForDatabase($idField->getKeyname());
        }
        else if($this->allowNullValue())
            return 'NULL';

        return false;
    }

    public function decodeEarlyFromDatabase($value)
    {
        $class = $this->getDataClass();
        $class->queueObjectUsingId($value);

        return $value;
    }

    public function decodeFromDatabase($value)
    {
        $class = $this->getDataClass();
        $obj = $class->getObjectUsingId($value);

        return $obj;
    }


    public function encodeForForm($value)
    {
        $class = $this->getDataClass();
        if($value instanceof MXT_DataObject
            && $value->getDataClass() instanceof $class)
        {
            return $value->getId();
        }

        return null;
    }

    public function decodeFromForm($value)
    {
        if($value instanceof MXT_Pair)
        {
            $class = $this->getDataClass();
            $obj = $class->getObjectUsingId($value->left);
            return $obj;
        }
        else if(is_numeric($value))
        {
            $class = $this->getDataClass();
            $obj = $class->getObjectUsingId($value);
            return $obj;
        }

        return null;
    }
}


?>
