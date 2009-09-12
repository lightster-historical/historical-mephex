<?php


require_once PATH_LIB . 'com/mephex/data-object/class/AbstractWritableDataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseDataClassReader.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseDataClassWriter.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractDatabaseHandler.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DateType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DateTimeType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/HiddenIdType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/NumericType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/TimeDurationType.php';

require_once PATH_LIB . 'com/mephex/data-object/field/DateDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DateTimeDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/DefaultDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/IntegerDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/FloatDataField.php';
require_once PATH_LIB . 'com/mephex/data-object/field/PrimaryKeyDataField.php';


abstract class MXT_AbstractDatabaseDataClass extends MXT_AbstractWritableDataClass
    implements MXT_DO_AbstractDatabaseHandler
{
    public function __construct()
    {
        parent::__construct();
    }


    protected function getCacheableFields()
    {
        $fields = new MXT_DO_Fields($this);

        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        $query = new Query('SHOW FIELDS FROM ' . $db->getTable($tableName));
        $result = $db->execQuery($query);
        while($row = $db->getAssoc($result))
        {
            $allowNull = ($row['Null'] == 'YES');
            $typeName = strtolower($row['Type']);

            if(preg_match('/\([0-9]+\)/', $typeName, $matches))
                $length = $matches[0];
            else
                $length = null;

            /*
            $type = new MXT_DefaultType($row['Default'], $allowNull);
            if($typeName == 'time')
                $type = new MXT_TimeDurationType($allowNull ? null : new MXT_TimeDuration(), $allowNull);
            else if($typeName == 'date')
                $type = new MXT_DateType($allowNull ? null : new Date(), $allowNull);
            */

            $keyname = $row['Field'];

            if(!(strpos($row['Extra'], 'auto_increment') === false))
                $field = new MXT_PrimaryKeyDataField($this, $keyname);
            else if(self::startsWith($typeName, 'int')
                || self::startsWith($typeName, 'tinyint')
                || self::startsWith($typeName, 'smallint')
                || self::startsWith($typeName, 'mediumint')
                || self::startsWith($typeName, 'bigint'))
                $field = new MXT_IntegerDataField($this, $keyname);
            else if(self::startsWith($typeName, 'decimal')
                || self::startsWith($typeName, 'numeric')
                || self::startsWith($typeName, 'real')
                || self::startsWith($typeName, 'float')
                || self::startsWith($typeName, 'double'))
                $field = new MXT_FloatDataField($this, $keyname);
            else if($typeName == 'datetime')
                $field = new MXT_DateTimeDataField($this, $keyname);
            else if($typeName == 'date')
                $field = new MXT_DateDataField($this, $keyname);
            else
                $field = new MXT_DefaultDataField($this, $keyname);

            $field->setAllowEmpty($allowNull);
            $field->setDefaultValue($row['Default']);
            $field->setLength($length);

            $fields->replace($field);
            if(!(strpos($row['Extra'], 'auto_increment') === false))
                $this->idField = $field;
        }

        return $fields;
    }


    protected function getDefaultReader()
    {
        return new MXT_DO_DatabaseDataClassReader($this);
    }

    protected function getDefaultWriter()
    {
        return new MXT_DO_DatabaseDataClassWriter($this);
    }


    public function getGeneralSelectSQL($where = '')
    {
        return $this->getReader()->getDefaultGeneralSelectSQL($where);
    }

    public function getSelectSQL($idName, $idValue)
    {
        return $this->getReader()->getDefaultSelectSQL($idName, $idValue);
    }

    public function getSelectAllSQL()
    {
        return $this->getReader()->getDefaultSelectAllSQL();
    }

    public function getWhereSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getReader()->getDefaultWhereSQLUsingFilter($filter);
    }

    public function getOrderBySQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getReader()->getDefaultOrderBySQLUsingFilter($filter);
    }

    public function getLimitSQLUsingFilter(MXT_DO_AbstractFilter $filter)
    {
        return $this->getReader()->getDefaultLimitSQLUsingFilter($filter);
    }

    public function getIdsWhereSQL($idName, array $encodedIds)
    {
        return $this->getReader()->getDefaultIdsWhereSQL($idName, $encodedIds);
    }


    public function getContentLastUpdated()
    {
        $parentModified = new Date(parent::getContentLastUpdated());
        $thisModified = new Date(filemtime(__FILE__));

        return Date::getMax($parentModified, $thisModified);
    }
}



?>
