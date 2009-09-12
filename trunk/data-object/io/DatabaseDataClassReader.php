<?php


require_once PATH_LIB . 'com/mephex/data-object/class/AbstractDatabaseDataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseReader.php';


class MXT_DO_DatabaseDataClassReader extends MXT_DO_DatabaseReader
{
    public function __construct(MXT_AbstractDatabaseDataClass $class)
    {
        parent::__construct($class, $class);
    }


    public function getGeneralSelectSQL($where = '')
    {
        return $this->getDataClass()->getGeneralSelectSQL($where);
    }

    public function getSelectSQL($idName, $idValue)
    {
        return $this->getDataClass()->getSelectSQL($idName, $idValue);
    }

    public function getSelectAllSQL()
    {
        return $this->getDataClass()->getSelectAllSQL();
    }

    public function getWhereSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDataClass()->getWhereSQLUsingFilter($filter);
    }

    public function getOrderBySQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDataClass()->getOrderBySQLUsingFilter($filter);
    }

    public function getLimitSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getDataClass()->getLimitSQLUsingFilter($filter);
    }

    public function getIdsWhereSQL($idName, array $encodedIds)
    {
        return $this->getDataClass()->getIdsWhereSQL($idName, $encodedIds);
    }
}



?>
