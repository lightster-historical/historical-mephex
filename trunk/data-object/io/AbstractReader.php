<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';


interface MXT_DO_AbstractReader
{
    public function loadObjectsUsingIds(array $ids);

    public function getTotalObjectCount();
    public function loadAllObjects();

    public function getTotalObjectCountUsingFilter(MXT_DO_AbstractFilter $filter);
    public function getAllObjectsUsingFilter(MXT_DO_AbstractFilter $filter);
}



?>
