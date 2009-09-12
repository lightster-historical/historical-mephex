<?php


require_once PATH_LIB . 'com/mephex/db/ConnectionException.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/db/QueryException.php';
require_once PATH_LIB . 'com/mephex/db/QueryResult.php';


abstract class Database
{
    protected static $connections = array();


    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $port;

    protected $tables;
    protected $tablePrefix;
    protected $tablePstfix;

    protected static $time = 0;


    public function __construct($host, $username, $password, $database,
        $port = null, $hash = null)
    {
        $this->host         = $host;
        $this->username     = $username;
        $this->password     = $password;
        $this->database     = $database;
        $this->port         = $port;

        $this->tables       = array();
        $this->tablePrefix  = '';
        $this->tablePstfix  = '';

        if(!is_null($hash) && !array_key_exists($hash, self::$connections))
        {
            self::$connections[$hash] = $this;
        }
    }



    public static function getConnection($hash)
    {
        if(array_key_exists($hash, self::$connections))
        {
            return self::$connections[$hash];
        }

        echo 'Error: \'' . $hash . '\' connection hash not found';
        return null;
    }

    public static function setHash(Database $conn, $hash)
    {
        if(!array_key_exists($hash, self::$connections))
        {
            $connection = clone $conn;
            $connection->tables = array();
            $connection->tablePrefix  = '';
            $connection->tablePstfix  = '';

            self::$connections[$hash] = $connection;
        }

        return self::$connections[$hash];
    }

    public static function removeHash($hash)
    {
        if(array_key_exists($hash, self::$connections))
        {
            unset(self::$connections[$hash]);
        }
    }



    public function setTablePrefix($prefix)
    {
        if(is_string($prefix))
        {
            $this->tablePrefix = $prefix;
        }
    }

    public function setTableName($key, $tableName)
    {
        $this->tables[$key] = $tableName;
    }

    public function getTable($key)
    {
        if (isset($this->tables[$key]))
        {
            return $this->tables[$key];
        }
        else
        {
            return $this->tablePrefix . $key . $this->tablePstfix;
        }
    }

    public function table($key)
    {
        return $this->getTable($key);
    }

    /*
    function date ($format = DB_DATETIME, $timeZone = 0)
    {
        //  create a Date object representing the current time
        $now = new Date(null, floatval($timeZone));

        // figure out which date format to use
        switch ($format)
        {
            //  date
            case DB_DATE:
                $date   = $now->format('q');
                break;
            //  time
            case DB_TIME:
                $date   = $now->format('Q');
                break;
            //  date/time
            default:
                $date   = $now->format('q Q');
                break;
        }

        return $date;
    }
    //*/

    abstract public function execQuery(Query $query);
    abstract public function getAutoIncrementId();
    abstract public function getInfo($type);
    abstract public function getCount(QueryResult $queryResult);
    abstract public function getAssoc(QueryResult $queryResult);
    abstract public function getRow(QueryResult $queryResult);

    public function /*get*/resultSet(QueryResult $query, $row = false, $index = null)
    {
        $resultSet  = array();

        // if the results should be fetched using the associate array method
        if (!$row)
        {
            // continue to loop while there are results
            while ($result = $this->getAssoc($query))
            {
                // if the supplied key (index) exists in the array of results
                if (array_key_exists($index, $result))
                {
                    // use the value the key represents to append the results
                    // to the result set
                    $resultSet[$result[$index]] = $result;
                }
                // if the key does not exist
                else
                {
                    // append the result to the result set with a natural index
                    $resultSet[] = $result;
                }
            }
        }
        else
        {
            // continue to loop while there are results
            while ($result = $this->getRow($query))
            {
                // if the supplied key (index) exists in the array of results
                if (array_key_exists($index, $result))
                {
                    // use the index'th value to append the results to the
                    // result set
                    $resultSet[$result[$index]] = $result;
                }
                else
                {
                    // append the rsult to the result set with a natural index
                    $resultSet[] = $result;
                }
            }
        }

        return $resultSet;
    }


    public static function getQueryCount()
    {
        return QueryResult::getQueryCount();
    }

    public static function getTime()
    {
        return self::$time;
    }
}


?>
