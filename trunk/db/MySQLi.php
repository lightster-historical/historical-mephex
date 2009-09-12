<?php


/**
 * @import com.mephex.db.ConnectionException
 * @import com.mephex.db.Database
 * @import com.mephex.db.QueryResult
 */
require_once PATH_LIB . 'com/mephex/db/ConnectionException.php';
require_once PATH_LIB . 'com/mephex/db/QueryException.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/QueryResult.php';


class MySQL extends Database
{
    protected $connection;
    protected $connected;


    function __construct($host, $username, $password, $database, $port = null)
    {
        parent::__construct($host, $username, $password, $database, $port, $host);

        $startTime = microtime(true);

        // if the port is non-empty, append it to the host
        if (!is_null($this->port))
        {
            $host .= ':' . $port;
        }

        self::setHash($this, $host . ':' . $database);

        if (!function_exists('mysql_connect'))
        {
            trigger_error('The MySQL library is not loaded.', E_USER_ERROR);
        }
        // if the database server and database were successfully connected to,
        // record the database connection
        else if (($this->connection =
            @mysql_connect($host, $this->username, $this->password, true)) &&
            @mysql_select_db($this->database, $this->connection))
        {
            $this->connected    = true;
        }

        // if a database connection does not exist
        if(!$this->connected)
        {
            throw new ConnectionException(mysql_error($this->connection));
        }

        self::$time += (microtime(true) - $startTime);
    }
    // constructor

    // retrieves information about the database connection
    function getInfo($type)
    {
        switch (strtolower($type))
        {
            case 'software':    return 'MySQL Database Server';
            case 'version':     return mysql_get_server_info();
            case 'type':        return 'mysql';
            default:            return null;
        }
    }
    // info method


    // queries the database with the given query string
    function execQuery(Query $sql)
    {
        $startTime = microtime(true);

        // query the database
        $query = mysql_query($sql->getQuery(), $this->connection);

        if(!$query)
        {
            throw new QueryException(mysql_error($this->connection), $sql->getQuery());
        }

        $time = (microtime(true) - $startTime);
        $sql->addTime($time);
        self::$time += $time;

        // return the query resource
        return new QueryResult($query);
    }
    // query method

    // creates an array of results using column names as array keys
    function getAssoc(QueryResult $query)
    {
        $startTime = microtime(true);
        $row = mysql_fetch_assoc($query->getResult());
        self::$time += (microtime(true) - $startTime);

        return $row;
    }
    // assoc method

    // creates an array of results using consecutive integers as array keys
    function getRow(QueryResult $query)
    {
        $startTime = microtime(true);
        $row = mysql_fetch_row($query->getResult());
        self::$time += (microtime(true) - $startTime);

        return $row;
    }
    // row method


    // retrieves the last auto increment id
    function getAutoIncrementId()
    {
        $startTime = microtime(true);
        $id = mysql_insert_id($this->connection);
        self::$time += (microtime(true) - $startTime);

        return $id;
    }

    // counts the number of rows returned or changed
    function getCount(QueryResult $query)
    {
        $startTime = microtime(true);
        $count = is_bool($query->getResult()) ?
            mysql_affected_rows($this->connection) : mysql_num_rows($query);
        self::$time += (microtime(true) - $startTime);

        return $count;
    }
    // count method
}
// MySQL class


?>
