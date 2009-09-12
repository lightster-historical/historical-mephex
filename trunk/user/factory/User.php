<?php


require_once PATH_LIB . 'com/lightdatasys/user/model/User.php';

require_once PATH_LIB . 'com/mephex/data-object/class/AbstractDatabaseDataClass.php';
#require_once PATH_LIB . 'com/mephex/data-object/type/DataObjectType.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';


class MXT_UserClass extends MXT_AbstractDatabaseDataClass
{
    protected static $singleton = null;


    public static function getSingleton()
    {
        return self::getSingletonUsingClassName(__CLASS__);
    }



    public function getDataObjectName()
    {
        return 'MXT_User';
    }


    public function getTableName()
    {
        return 'User';
    }

    public function getDbConnection()
    {
        return Database::getConnection('com.lightdatasys.nfl');
    }


    public function getCacheableFields()
    {
        $fields = parent::getCacheableFields();

        $fields->addPasswordFields('password', 'securityHash', 'passwordDate');

        return $fields;
    }


    public function getClassFileName()
    {
        return __FILE__;
    }


    /*
    public function getGeneralSelectSQL($where = '')
    {
        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        return 'SELECT mt.*, COUNT(race.raceId) AS raceCount FROM '
            . $db->getTable($tableName) . ' AS mt'
            . ' LEFT JOIN ' . $db->getTable('Race') . ' AS race'
            . ' ON mt.seasonId=race.seasonId'
            . $where
            . ' GROUP BY mt.seasonId';
    }

    public function getSelectAllSQL()
    {
        return $this->getGeneralSelectSQL() . ' ORDER BY mt.year DESC';
    }


    public function getUsingSeriesAndYear(LDS_Series $series, $year)
    {
        return $this->getUsingSeriesIdAndYear($series->getId(), $year);
    }

    public function getUsingSeriesIdAndYear($seriesId, $year)
    {
        $seriesId = intval($seriesId);
        $year = intval($year);

        /*if(self::$cacheBySeriesIdAndYear->containsKey($seriesId . $year))
            return self::$cacheBySeriesIdAndYear->get($seriesId . $year);
        else*//* if($seriesId > 0 && $year > 0)
        {
            $db = $this->getDbConnection();

            $query = new Query('SELECT season.*, COUNT(race.raceId) AS raceCount FROM '
                . $db->getTable('Season') . ' AS season'
                . ' INNER JOIN ' . $db->getTable('Race') . ' AS race'
                . ' ON season.seasonId=race.seasonId AND race.forPoints=1'
                . ' WHERE season.year=' . $year
                . ' AND season.seriesId=' . $seriesId
                . ' GROUP BY season.seasonId');
            return $this->constructUsingQuery($query);
        }

        return null;
    }

    public function getUsingSeries(LDS_Series $series)
    {
        return $this->getUsingSeriesId($series->getId());
    }

    public function getUsingSeriesId($seriesId)
    {
        $seriesId = intval($seriesId);

        //if(self::$cacheBySeriesId->containsKey($seriesId))
        //    return self::$cacheBySeriesId->get($seriesId);
        //else
        if($seriesId > 0)
        {
            $db = $this->getDbConnection();

            $query = new Query('SELECT season.*, COUNT(race.raceId) AS raceCount FROM '
                . $db->getTable('Season') . ' AS season'
                . ' INNER JOIN ' . $db->getTable('Race') . ' AS race'
                . ' ON season.seasonId=race.seasonId AND race.forPoints=1'
                . ' WHERE season.seriesId=' . $seriesId
                . ' GROUP BY season.seasonId ORDER BY season.year DESC LIMIT 1');
            return $this->getReader()->constructUsingQuery($query);
        }

        return null;
    }
    */
}



?>
