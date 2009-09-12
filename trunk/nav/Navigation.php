<?php


require_once PATH_LIB . 'com/mephex/dev/Debug.php';
#MXT_Debug::logDeprecation('com.mephex.nav.Navigation', 'com.mephex.nav.DatabaseNavigation');



/**
 * @import com.mephex.nav.NavItem
 */
require_once PATH_LIB . 'com/mephex/nav/NavItem.php';


class Navigation
{
    protected static $navsByKeyname = array();
    protected static $toLoad = array();


    protected $loaded;

    protected $rootItems;
    protected $fosterItems; // they have no parents! :-\


    public function __construct()
    {
        $this->loaded = false;

        $this->rootItems = array();
        $this->fosterItems = array();
    }


    public function getRootItems()
    {
        if(!$this->loaded)
            self::loadToLoads();

        return $this->rootItems;
    }

    public function getFosterItems()
    {
        if(!$this->loaded)
            self::loadToLoads();

        return $this->fosterItems;
    }



    public static function getKeysFromFileName($fileName)
    {
        $keys = explode('/', substr(dirname($_SERVER['PHP_SELF']), 1));
        $base = basename($_SERVER['PHP_SELF']);
        $keys[] = $base;

        return $keys;
    }


    public function getItemFromFileName($fileName, $strict = false)
    {
        $keys = self::getKeysFromFileName($fileName);
        return $this->getItemFromKeys($keys, $strict);
    }

    public function getItemFromKeys($keys, $strict = false)
    {
        $item = null;

        $items = $this->getRootItems();
        foreach($keys as $key)
        {
            if(count($items) <= 0)
            {
                if($strict)
                    return null;
                else
                    return $item;
            }

            $nextItem = self::getFromKey($items, $key);
            if(is_null($nextItem))
            {
                if($strict)
                    return null;
                else
                    return $item;
            }
            $item = $nextItem;

            $items = $item->getChildren();
        }

        return $item;
    }

    public static function getFromParent(Navigation $nav, NavItem $parent = null)
    {
        if(!is_null($parent))
        {
            return $parent->getChildren();
        }
        else
        {
            return $nav->getRootItems();
        }
    }

    public static function getFromKey($navItems, $key)
    {
        foreach($navItems as $navItem)
        {
            if($navItem->getKeyName() == $key)
                return $navItem;
        }

        return null;
    }


    public static function getNavigation($keyname)
    {
        if(!array_key_exists($keyname, self::$navsByKeyname))
        {
            $nav = new Navigation();

            $nav->rootItems = array();
            $nav->fosterItems = array();

            self::$navsByKeyname[$keyname] = $nav;
            self::$toLoad[$keyname] = '\'' . addslashes($keyname) . '\'';
        }
        else
            $nav = self::$navsByKeyname[$keyname];

        return $nav;
    }

    public static function loadToLoads()
    {
        $db = Database::getConnection('com.mephex.nav');

        if(count(self::$toLoad) > 0)
        {
            $nav = null;
            $lastKeyname = '';

            $navItems = array();

            $sql = new Query('SELECT itemId, parentId, title, ni.keyName, link, '
                . 'permissionDomain, permission, useQS, n.keyName AS navKeyName FROM ' . $db->getTable('navItem')
                . ' AS ni INNER JOIN ' . $db->getTable('nav') . ' AS n'
                . ' ON ni.navId=n.navId WHERE n.keyName IN ('
                . implode(',', self::$toLoad)
                . ') ORDER BY n.keyName ASC, orderIndex ASC, title ASC');
            $query = $db->execQuery($sql);
            while($row = $db->getRow($query))
            {
                if($lastKeyname != $row[8])
                {
                    $nav = self::getNavigation($row[8]);
                    $lastKeyname = $row[8];
                }

                if($row[1] == 0)
                {
                    $item = new NavItem($row[0], $row[3], $row[2], null
                        , $row[4], $row[5], $row[6], $row[7]);
                    $nav->rootItems[$row[0]] = $item;
                    $navItems[$row[0]] = $item;
                }
                else
                {
                    $item = new NavItem($row[0], $row[3], $row[2], null
                        , $row[4], $row[5], $row[6], $row[7]);
                    $nav->fosterItems[$row[1]][$row[0]] = $item;
                    $navItems[$row[0]] = $item;
                }
            }

            foreach(self::$toLoad as $navKeyname => $quoted)
            {
                $nav = self::getNavigation($navKeyname);

                $change = true;
                while($change)
                {
                    $change = false;

                    foreach($nav->fosterItems as $pId => $items)
                    {
                        if(array_key_exists($pId, $navItems))
                        {
                            foreach($items as $id => $item)
                            {
                                $item->setParent($navItems[$pId]);
                                $change = true;
                            }

                            unset($nav->fosterItems[$pId]);
                        }
                    }
                }

                $nav->loaded = true;
            }

            self::$toLoad = array();
        }
    }
}


?>
