<?php


/**
 * @import com.mephex.nav.NavItem
 */
require_once PATH_LIB . 'com/mephex/nav/Navigation.php';
require_once PATH_LIB . 'com/mephex/nav/NavItem.php';


class NavIterator
{
    public function __construct()
    {
    }


    public function printItem(NavItem $item, $keys, $depth, $parentSelected = true)
    {
        $selected = false;
        if($depth < count($keys) && $keys[$depth] == $item->getKeyName())
        {
            $selected = $parentSelected;
        }

        $items = $item->getChildren();
        foreach($items as &$item)
        {
            $permissionDomain = $item->getPermissionDomain();
            $permission = $item->getPermission();

            if($permissionDomain == '' || $permission == ''
                || Permission::getPermission(User::getActiveUser(), $permissionDomain, $permission))
            {
                $this->printItem($item, $keys, $depth + 1, $selected);
            }
        }
    }

    public function printNavigation(Navigation $nav, $keys)
    {
        $items = $nav->getRootItems();
        foreach($items as &$item)
        {
            $permissionDomain = $item->getPermissionDomain();
            $permission = $item->getPermission();

            if($permissionDomain == '' || $permission == ''
                || Permission::getPermission(User::getActiveUser(), $permissionDomain, $permission))
            {
                $this->printItem($item, $keys, 0);
            }
        }
    }
}


?>
