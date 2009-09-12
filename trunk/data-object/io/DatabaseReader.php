<?php


require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractDatabaseHandler.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractReader.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseDecoder.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseEncoder.php';


class MXT_DO_DatabaseReader implements MXT_DO_AbstractReader
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

    protected function getDbConnection()
    {
        return $this->getDatabaseHandler()->getDbConnection();
    }

    protected function getTableName()
    {
        return $this->getDatabaseHandler()->getTableName();
    }


    public function getDecoder()
    {
        return new MXT_DO_DatabaseDecoder();
    }

    public function getEncoder()
    {
        return $this->getDatabaseEncoder();
    }

    public function getDatabaseEncoder()
    {
        return new MXT_DO_DatabaseEncoder();
    }




    public function loadObjectsUsingIds(array $ids)
    {
        $class = $this->getDataClass();
        $db = $this->getDbConnection();

        $idField = $class->getIdField();
        $idName = $idField->getKeyname();

        if(count($ids) > 0)
        {
            $query = new Query(
                $this->getGeneralSelectSQL(
                    $this->getIdsWhereSQL($idName, $ids)));
            $this->constructUsingQuery($query);
        }
    }


    public function getTotalObjectCount()
    {
        $class = $this->getDataClass();

        $idField = $class->getIdField();
        $idName = $idField->getKeyname();
        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        $query = new Query('SELECT COUNT(`' . $idName . '`) FROM '
            . $db->getTable($tableName));
        $result = $db->execQuery($query);
        if($row = $db->getRow($result))
            return $row[0];
        return 0;
    }

    public function loadAllObjects()
    {
        $db = $this->getDbConnection();

        $query = new Query($this->getSelectAllSQL());
        $this->constructUsingQuery($query);
    }


    public function getTotalObjectCountUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        $where = $this->getWhereSQLUsingFilter($filter);

        $class = $this->getDataClass();

        $idField = $class->getIdField();
        $idName = $idField->getKeyname();
        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        $query = new Query('SELECT COUNT(`' . $idName . '`) FROM '
            . $db->getTable($tableName) . $where);
        $result = $db->execQuery($query);
        if($row = $db->getRow($result))
            return $row[0];
        return 0;
    }

    public function getAllObjectsUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        $db = $this->getDbConnection();

        $where = $this->getWhereSQLUsingFilter($filter);
        $orderBy = $this->getOrderBySQLUsingFilter($filter);
        $limit = $this->getLimitSQLUsingFilter($filter);

        $objs = array();

        $query = new Query($this->getGeneralSelectSQL($where) . $orderBy . $limit);
        $result = $db->execQuery($query);
        while($row = $db->getAssoc($result))
        {
            $obj = $this->constructUsingRow($row);

            if(!is_null($obj))
                $objs[$obj->getId()] = $obj;
        }

        return $objs;
    }






    public function getGeneralSelectSQL($where = '')
    {
        return $this->getDefaultGeneralSelectSQL($where);
    }

    public function getSelectSQL($idName, $idValue)
    {
        return $this->getDefaultSelectSQL($idName, $idValue);
    }

    public function getSelectAllSQL()
    {
        return $this->getDefaultSelectAllSQL();
    }

    public function getWhereSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDefaultWhereSQLUsingFilter($filter);
    }

    public function getOrderBySQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDefaultOrderBySQLUsingFilter($filter);
    }

    public function getLimitSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDefaultLimitSQLUsingFilter($filter);
    }

    public function getIdsWhereSQL($idName, array $encodedIds)
    {
        return $this->getDefaultIdsWhereSQL($idName, $encodedIds);
    }


    public function getDefaultGeneralSelectSQL($where = '')
    {
        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        // mt=main table
        return 'SELECT mt.* FROM ' . $db->getTable($tableName)
            . ' AS mt' . $where;
    }

    public function getDefaultSelectSQL($idName, $idValue)
    {
        return $this->getGeneralSelectSQL(' WHERE mt.`' . $idName . '`=' . $idValue);
    }

    public function getDefaultSelectAllSQL()
    {
        return $this->getGeneralSelectSQL();
    }

    public function getDefaultWhereSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        $class = $this->getDataClass();

        $sql = '';

        $filterValues = $filter->getFilterValues();
        $wheres = array();
        if(count($filterValues) > 0)
        {
            foreach($filterValues as $keyname => $values)
            {
                if($class->isField($keyname))
                {
                    $field = $class->getField($keyname);

                    if($field instanceof MXT_DO_AbstractFilterable)
                    {
                        if(!is_array($values))
                            $values = array($values);

                        $encoded = array();
                        foreach($values as $value)
                        {
                            $encodedValue = $field->encodeValue($value, $this->getDatabaseEncoder());
                            if($encodedValue)
                                $encoded[] = $encodedValue;
                        }

                        $filterKeyname = $field->getFilterableFieldKeyname();
                        if(count($encoded) == 1)
                            $wheres[] = "`$filterKeyname`=" . implode('', $encoded);
                        else if(count($encoded) > 1)
                            $wheres[] = "`$filterKeyname` IN(" . implode(',', $encoded) . ')';
                    }
                }
            }

            if(count($wheres) > 0)
            {
                $sql = ' WHERE (' . implode(' AND ', $wheres) . ')';
            }
        }

        return $sql;
    }

    public function getDefaultOrderBySQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        $class = $this->getDataClass();

        $sql = '';

        $sortFields = $filter->getSortFields();
        $orderBys = array();
        if(count($sortFields) > 0)
        {
            foreach($sortFields as $keyname => $direction)
            {
                if($class->isField($keyname))
                {
                    $direction = strtoupper($direction);
                    if($direction == 'DESC')
                        $orderBys[] = $keyname . ' DESC';
                    else
                        $orderBys[] = $keyname;
                }
            }

            if(count($orderBys) > 0)
            {
                $sql = ' ORDER BY ' . implode(', ', $orderBys);
            }
        }

        return $sql;
    }

    public function getDefaultLimitSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        $count = $filter->getLimitCount();
        $offset = $filter->getLimitOffset();

        if($count > 0)
        {
            if($offset < 0)
                $offset = 0;

            return " LIMIT $offset, $count";
        }

        return '';
    }

    public function getDefaultIdsWhereSQL($idName, array $encodedIds)
    {
        if(count($encodedIds) == 1)
            return ' WHERE mt.`' . $idName . '`=' . reset($encodedIds);
        else
            return ' WHERE mt.`' . $idName . '` IN (' . implode(',', $encodedIds) . ')';
    }


    public function constructUsingQuery(Query $query)
    {
        $db = $this->getDbConnection();

        $obj = null;

        $result = $db->execQuery($query);
        while($row = $db->getAssoc($result))
        {
            $obj = $this->constructUsingRow($row);
        }

        return $obj;
    }

    public function constructUsingRow(array $row)
    {
        $class = $this->getDataClass();

        $idField = $class->getIdField();
        $idName = $idField->getKeyname();
        $id = Utility::getValueUsingKey($row, $idName);

        if(!$class->getCache()->containsKey($id))
        {
            $obj = $class->getCreatedObject();
            $this->initValuesUsingRow($obj, $row);

            $class->getCache()->add($id, $obj);
        }
        else
        {
            $obj = $class->getCache()->get($id);
            $class->getCache()->set($id, $obj);
        }

        return $obj;
    }

    public function initValuesUsingRow(MXT_DataObject $obj, array $row)
    {
        $obj->initEncodedValues($row, $this->getDecoder());

        return $obj;
    }



    /*
    public function initObjectUsingIdAsUnloaded(MXT_DataObject $obj, $id)
    {
        $obj->setLoaded(false);
        $obj->setId($id);

        $this->cacheById->add($obj->getId(), $obj);

        return $obj;
    }

    public function initObjectUsingIdAsLoaded(MXT_DataObject $obj, $id)
    {
        $db = $this->getDbConnection();

        $idField = $this->getIdField();
        $idName = $idField->getKeyname();
        $idValue = $idField->encodeForDatabase($id);

        $query = new Query($this->getSelectSQL($idName, $idValue));
        $result = $db->execQuery($query);
        if($row = $db->getAssoc($result))
            $this->initValuesUsingRow($obj, $row);

        $this->cacheById->add($obj->getId(), $obj);

        return $obj;
    }
    */
}



?>
