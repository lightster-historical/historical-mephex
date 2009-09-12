<?php


require_once PATH_LIB . 'com/mephex/data-object/field/AbstractDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/BooleanDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/ForeignKeyDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/ForeignObjectDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/ModifiedTimeDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/PasswordDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/SaltDataField.php';


class MXT_DO_Fields
{
    protected $dataClass;
    protected $fields;


    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
        $this->fields = array();
    }


    public function getDataClass()
    {
        return $this->dataClass;
    }

    public function getFields()
    {
        return $this->fields;
    }


    public function get($keyname)
    {
        if(array_key_exists($keyname, $this->fields))
            return $this->fields[$keyname];

        return null;
    }


    public function add(MXT_AbstractDataField $field)
    {
        $this->remove($field->getKeyname());
        $this->replace($field);
    }

    public function replace(MXT_AbstractDataField $field)
    {
        $this->fields[$field->getKeyname()] = $field;
    }

    public function remove($keyname)
    {
        if(array_key_exists($keyname, $this->fields))
            unset($this->fields[$keyname]);
    }


    public function addForeignObjectFields($keyname, $idKeyname, $foreignClass)
    {
        $this->replace(new MXT_ForeignObjectDataField(
            $this->getDataClass(), $keyname, $idKeyname, $foreignClass));

        if(array_key_exists($idKeyname, $this->getFields()))
        {
            $this->replace(
                MXT_ForeignKeyDataField::convertFromIntegerDataField(
                    $this->get($idKeyname), $keyname));
        }
        else
        {
            $this->replace(
                new MXT_ForeignKeyDataField($this->getDataClass(), $idKeyname, $keyname));
        }
    }

    public function addModifiedTimeField($keyname)
    {
        if(array_key_exists($keyname, $this->getFields()))
        {
            $this->replace(
                MXT_ModifiedTimeDataField::convertDataField($this->get($keyname)));
        }
        else
        {
            $this->replace(
                new MXT_ModifiedTimeDataField($this->getDataClass(), $keyname));
        }
    }

    public function addBooleanDataField($keyname)
    {
        if(array_key_exists($keyname, $this->getFields()))
        {
            $this->replace(
                MXT_BooleanDataField::convertDataField($this->get($keyname)));
        }
        else
        {
            $this->replace(
                new MXT_BooleanDataField($this->getDataClass(), $keyname));
        }
    }

    public function addPasswordFields($keyname, $saltKeyname = null
        , $dateTimeKeyname = null)
    {
        $saltField = null;
        $dateTimeField = null;

        if(!is_null($saltKeyname))
        {
            if(array_key_exists($saltKeyname, $this->getFields()))
            {
                $saltField =
                    MXT_SaltDataField::convertFromDefaultDataField(
                        $this->get($saltKeyname));
                $this->replace($saltField);
            }
            else
            {
                throw new Exception();
            }
        }

        if(!is_null($dateTimeKeyname))
        {
            if(array_key_exists($dateTimeKeyname, $this->getFields()))
            {
                $dateTimeField = $this->get($dateTimeKeyname);
            }
            else
            {
                throw new Exception();
            }
        }

        if(array_key_exists($keyname, $this->getFields()))
        {
            $this->replace(
                MXT_PasswordDataField::convertFromDefaultDataField(
                    $this->get($keyname), $saltField, $dateTimeField));
        }
        else
        {
            throw new Exception();
        }
    }
}



?>