<?php


require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractDatabaseHandler.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractWriter.php';


class MXT_DO_DatabaseWriter implements MXT_DO_AbstractWriter
{
    protected $class;
    protected $dbHandler;


    public function __construct(MXT_DataClass $class, MXT_DO_AbstractDatabaseHandler $dbHandler)
    {
        $this->class = $class;
        $this->dbHandler = $dbHandler;
    }


    public function getDataClass()
    {
        return $this->class;
    }

    public function getDatabaseHandler()
    {
        return $this->dbHandler;
    }
    

    public function getEncoder()
    {
        return $this->getDatabaseEncoder();
    }

    public function getDatabaseEncoder()
    {
        return new MXT_DO_DatabaseEncoder();
    }


    public function create(MXT_DataObject $obj)
    {
        $class = $this->getDataClass();
        $dbHandler = $this->getDatabaseHandler();

        $tableName = $dbHandler->getTableName();
        $db = $dbHandler->getDbConnection();

        $keys = array();
        $values = array();
        $idField = $class->getIdField();
        $fields = $class->getFields();
        foreach($fields as $field)
        {
            if($field != $idField && $field->isStored())
            {
                $value = $field->encodeValue($obj->getValue($field->getKeyname()), $this->getEncoder());
                    
                if($field->isEmptyAllowed() && $field->isValueEmpty($value))
                {
                    $keys[] = '`' . $field->getKeyname() . '`';
                    $values[] = 'NULL';
                }
                else if(!($value === false) && $value != '')
                {
                    $keys[] = '`' . $field->getKeyname() . '`';
                    $values[] = $value;
                }
            }
        }

        if(count($keys) > 0 && count($keys) == count($values))
        {
            $query = new Query('INSERT INTO ' . $db->getTable($tableName)
                . ' (' . implode(', ', $keys) . ') VALUES ('
                . implode(', ', $values) . ')');
            #echo $query->getQuery();
            if($result = $db->execQuery($query))
            {
                $obj->setId($db->getAutoIncrementId());
                return $obj;
            }
        }

        return null;
    }

    public function update(MXT_DataObject $obj)
    {
        $class = $this->getDataClass();
        $dbHandler = $this->getDatabaseHandler();

        $tableName = $dbHandler->getTableName();
        $db = $dbHandler->getDbConnection();

        $keyValues = array();
        $idField = $class->getIdField();
        $fields = $class->getFields();
        foreach($fields as $field)
        {
            if($field != $idField && $field->isStored() && $obj->isValueChanged($field->getKeyname()))
            {
                $value = $field->encodeValue($obj->getValue($field->getKeyname()), $this->getEncoder());

                if($field->isEmptyAllowed() && $field->isValueEmpty($value))
                    $keyValues[] = '`' . $field->getKeyname() . '`=NULL';
                else if(!($value === false) && $value != '')
                    $keyValues[] = '`' . $field->getKeyname() . '`=' . $value;
            }
        }
        
        $value = $idField->encodeValue($obj->getValue($idField->getKeyname()), $this->getEncoder());
        $where = ' WHERE `' . $idField->getKeyname() . '`=' . $value;

        if(count($keyValues) > 0)
        {
            $query = new Query('UPDATE ' . $db->getTable($tableName)
                . ' SET ' . implode(', ', $keyValues) . $where);
            //echo $query->getQuery();
            if($result = $db->execQuery($query))
                return $obj;
        }

        return null;
    }
}



?>
