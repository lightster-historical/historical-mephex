<?php



require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/user/Permission.php';
require_once PATH_LIB . 'com/mephex/user/PermissionDomain.php';



class Group
{
    protected static $groups = array();
	protected static $namesToLoad = array();
	protected static $valuesToLoad = array();
    
    
    protected $groupId;
    protected $name;
    
    protected $permissions;
    protected $permissionsInherited;
    protected $permissionValues;
    
    
    
    protected function __construct($groupId)
    {
        $this->groupId = $groupId;
        $this->name = null;
        
        $this->permissions = array();
        $this->permissionsInherited = array();
        $this->permissionValues = array();
    }
	
	
    
    public static function getGroup($groupId)
    {        
        $db = Database::getConnection('com.mephex.user');
        
        $group = null;
        
        $groupId = intval($groupId);
        if($groupId >= 0)
        {
            if(array_key_exists($groupId, self::$groups))
            {
                $group = self::$groups[$groupId];
            }
            else
            {
				$group = new Group($groupId);
				self::$groups[$groupId] = $group;
				self::$namesToLoad[] = $groupId;
				self::$valuesToLoad[] = $groupId;
            }
        }
        else
        {
            trigger_error('Invalid groupId: ' . $groupId);
        }

        return $group;
    }
    
        
    protected static function loadNamesToLoad()
    {
        if(count(self::$namesToLoad) > 0)
        {
            $db = Database::getConnection('com.mephex.user');
            
            $toLoad = array();
            foreach(self::$namesToLoad as $group)
                $toLoad[] = intval($group);
            self::$namesToLoad = array();
				
			$query = new Query('SELECT groupId, name FROM userGroup WHERE groupId IN (' 
				. implode(',', $toLoad) . ')');
			$result = $db->execQuery($query);
			while($row = $db->getRow($result))
			{
				self::$groups[$row[0]]->name = $row[1];
			}
        }
    }
	
    protected static function loadValuesToLoad()
    {
        if(count(self::$valuesToLoad) > 0)
        {
            $db = Database::getConnection('com.mephex.user');
            
            $toLoad = array();
            foreach(self::$valuesToLoad as $group)
                $toLoad[] = intval($group);
            self::$valuesToLoad = array();
			
			$query = new Query('SELECT groupId, pd.domain, p.index, up.value '
				. 'FROM userGroup_permission AS up '
				. 'INNER JOIN permission AS p ON up.permissionId=p.permissionId '
				. 'INNER JOIN permissionDomain AS pd ON p.domainId=pd.domainId '
				. 'WHERE groupId IN (' . implode(',', $toLoad) . ')');
			$result = $db->execQuery($query);
			while($row = $db->getRow($result))
			{
				self::$groups[$row[0]]->permissionValues[$row[1]][$row[2]] = $row[3];
			}
        }
    }
    
    
    
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    public function getName()
    {
		self::loadNamesToLoad();
		
        return $this->name;
    }
    
    public function getPermission($domain, $keyName, $allowInherit = true)
    {
		self::loadValuesToLoad();
		
        $pDomain = PermissionDomain::getInstance($domain);
        
        if(!is_null($pDomain))
            $index = $pDomain->getIndex($keyName);
               
        if(!is_null($index))
        {
            if($allowInherit
                && array_key_exists($domain, $this->permissionsInherited)
                && array_key_exists($index, $this->permissionsInherited[$domain]))
            {
                return $this->permissionsInherited[$domain][$index];
            }
            else if(array_key_exists($domain, $this->permissions)
                && array_key_exists($index, $this->permissions[$domain]))
            {
                if(!$allowInherit)
                {
                    return $this->permissions[$domain][$index];
                }
                else
                {
                    $permission = $this->permissions[$domain][$index];
                    if(is_null($permission))
                        $permission = $pDomain->getPermission($keyName);
                    
                    $this->permissionsInherited[$domain][$index] = $permission;
                    
                    return $this->getPermission($domain, $keyName, $allowInherit);
                }
            }
            else if(array_key_exists($domain, $this->permissionValues))
            {
                $digit = $index % 5;
                $group = (($index - $digit) / 5) + 1;
                
                $value = $this->permissionValues[$domain][$group];
                $values = Permission::decode($value);
                
                for($i = 0; $i < 5; $i++)
                {
                    $this->permissions[$domain][$group * 5 + $i - 5] = $values[$i];
                }
                
                return $this->getPermission($domain, $keyName, $allowInherit);
            }
            else
            {
                $digit = $index % 5;
                $group = (($index - $digit) / 5) + 1;
                
                $this->permissionValues[$domain][$group] = Permission::INHERIT_ALL;
                
                return $this->getPermission($domain, $keyName, $allowInherit);
            }
        }
        
        return null;
    }
}


?>
