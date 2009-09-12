<?php


require_once PATH_LIB . 'com/mephex/nav/AbstractNavItem.php';


interface MXT_AbstractNavigation
{
    public function getRootItems();
    public function addRootItem(MXT_AbstractNavItem $item);
    
    public function getItemFromKeys(array $keys, $strict = false);
    
    public function setVariable($keyname, $value);
    public function getVariable($keyname);
    public function getUrlEncodedVariable($keyname);
}


?>
