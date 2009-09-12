<?php



require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/core/Utility.php';
require_once PATH_LIB . 'com/mephex/user/Permission.php';



abstract class PermissionDomain
{
    public static $domains = array();
    protected static $toLoad = array();



    protected $domainId;
    protected $domain;

    protected $permissions;
    protected $values;



    protected function __construct($domain)
    {
        #echo 'a';
        $this->domainId = null;
        $this->domain = $domain;
        $this->permissions = array();
        $this->values = array();

        self::$toLoad[] = $domain;
        self::$domains[$domain] = $this;
    }


    protected abstract function setKeyNames();



    protected static function loadToLoads()
    {
        //if(count(debug_backtrace()) > 25)
        //{ob_end_clean(); echo '<pre>'; print_r(User::getActiveUser()); debug_print_backtrace(); print_r(self::$domains); echo '</pre>';exit;}
        if(count(self::$toLoad) > 0)
        {
            $db = Database::getConnection('com.mephex.user');

            $toLoad = array();
            foreach(self::$toLoad as $domain)
                $toLoad[] = '\'' . addslashes($domain) . '\'';
            self::$toLoad = array();

            $domainsById = array();

            $query = new Query('SELECT domain, domainId FROM permissionDomain '
                . 'WHERE domain IN (' . implode(',', $toLoad) . ')');
            $result = $db->execQuery($query);
            while($row = $db->getRow($result))
            {
                self::$domains[$row[0]]->domainId = $row[1];
                $domainsById[$row[1]] = $row[0];
            }

            $query = new Query('SELECT domainId, `index`, value FROM permission '
                . 'WHERE domainId IN (' . implode(',', array_keys($domainsById)) . ')');
            $result = $db->execQuery($query);
            while($row = $db->getRow($result))
            {
                self::$domains[$domainsById[$row[0]]]->values[$row[1]] = $row[2];
            }

            foreach(self::$domains as $d)
            {
                $d->setKeyNames();
            }
        }
    }


    protected function setKeyName($index, $keyName)
    {
        self::loadToLoads();

        if($index < 0)
        {
            $this->permissions[$keyName] = $index;
        }
        else
        {
            $digit = $index % 5;
            $group = (($index - $digit) / 5) + 1;

            if(array_key_exists($group, $this->values))
            {
                $this->permissions[$keyName] = $index;

                return true;
            }
        }

        return false;
    }


    public function getDomainId()
    {
        self::loadToLoads();
        return $this->domainId;
    }

    public function getDomain()
    {
        self::loadToLoads();
        return $this->domain;
    }

    public function getIndex($keyName)
    {
        self::loadToLoads();

        if(!array_key_exists($keyName, $this->permissions))
            return null;

        return $this->permissions[$keyName];
    }

    public function getPermission($keyName)
    {
        self::loadToLoads();

        $index = $this->getIndex($keyName);
        if(is_null($index))
            return null;

        $digit = $index % 5;
        $group = (($index - $digit) / 5) + 1;

        if(!is_array($this->values[$group]))
        {
            $this->values[$group] = Permission::decode($this->values[$group]);
        }

        return ($this->values[$group][$digit] === true ? true : false);
    }


    public static function getInstance($domain = null)
    {
        #echo '<pre>'; debug_print_backtrace(); print_r(self::$domains); echo '</pre>';
        if(array_key_exists($domain, self::$domains))
            return self::$domains[$domain];
        else
            return null;
    }


    public static function loadAll()
    {
        $db = Database::getConnection('com.mephex.user');

        $domainsById = array();

        $query = new Query('SELECT domain, domainId, path, className'
            . ' FROM permissionDomain');
        $result = $db->execQuery($query);
        while($row = $db->getRow($result))
        {
            $path = Utility::verifyLibraryPath($row[2]);
            if(!($path === false))
            {
                require_once $path;

                $className = $row[3];
                self::$domains[$row[0]] = call_user_func(array($className, 'getInstance'), $row[0]);
                self::$domains[$row[0]]->domainId = $row[1];
                $domainsById[$row[1]] = $row[0];
            }
        }

        $query = new Query('SELECT domainId, `index`, value FROM permission '
            . 'WHERE domainId IN (' . implode(',', array_keys($domainsById)) . ')');
        $result = $db->execQuery($query);
        while($row = $db->getRow($result))
        {
            self::$domains[$domainsById[$row[0]]]->values[$row[1]] = $row[2];
        }

        self::$toLoad = array();

        foreach(self::$domains as $d)
        {
            $d->setKeyNames();
        }
    }
}


?>
