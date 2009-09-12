<?php



require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';



class PermissionDomain
{
    protected static $domains = array();



    protected $domainId;
    protected $domain;

    protected $permissions;
    protected $permissionSets;
    protected $values;



    private function __construct($permissi)
    {
        $db = Database::getConnection('com.lightdatasys.alpha');

        $query = new Query('SELECT domainId FROM permissionDomain '
            . 'WHERE domain=\'' . addslashes($domain) . '\'');
        $result = $db->execQuery($query);
        if($row = $db->getRow($result))
            $this->domainId = $row[0];
        else
            $this->domainId = null;

        $this->permissions = array();
        $this->values = array();

        if(!is_null($this->domainId))
        {
            $query = new Query('SELECT index, value FROM permission '
                . 'WHERE domainId=' . $this->domainId);
            $result = $db->execQuery($query);
            while($row = $db->getRow($result))
            {
                $this->values[$row[0]] = $row[1];
            }
        }
    }


    public function getDomainId()
    {
        return $this->domainId;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getPermission($index)
    {
        if(array_key_exists($index, self::$permissions))
            return self::$permissions[$index];
        else
            return null;
    }






    public static function getInstance($domain)
    {
        if(!array_key_exists($domain, self::$domains))
            self::$domains[$domain] = new PermissionDomain($domain);

        return self::$domains[$domain];
    }


    /*
    public static function loadAll()
    {
        $db = Database::getConnection('com.lightdatasys.alpha');

        #pemissionId	domainId	index	value
        $query = new Query('SELECT pd.domainId, pd.domain, p.permissionId, '
            . 'p.index, p.value FROM permissionDomain AS pd '
            . 'INNER JOIN permission AS p ON pd.domainId=p.domainId ORDER BY index ASC');
        $result = $db->execQuery($query);
        while($row = $db->getRow($result))
        {
            if(array_key_exists($row[1], self::$domains))
                $domain = self::$domains[$row[1]];
            else
                $domain = new PermissionDomain($row[0], $row[1]);

            $domain->addValue($row[3], $row[4]);
        }
    }
    */






    public static function getGroupById($groupId)
    {
        $groupId = intval($groupId);

        if(array_key_exists($groupId, self::$groupsById))
            return self::$groupsById[$groupId];

        return null;
    }

    public static function getGroupByDomain($domain)
    {
        if(array_key_exists($domain, self::$groupsByDomain))
            return self::$groupsByDomain[$domain];

        return null;
    }


    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getValue($keyname)
    {
    }


    public function addPermission($domain, Permission $permission)
    {
    }
}


?>
