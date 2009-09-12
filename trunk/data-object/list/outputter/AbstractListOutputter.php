<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/list/AbstractList.php';
require_once PATH_LIB . 'com/mephex/util/context/AbstractContextable.php';


interface MXT_DO_AbstractListOutputter extends MXT_AbstractContextable
{
    public function outputList(MXT_DO_AbstractList $list);
    public function outputListObject(MXT_DO_AbstractList $list, $i);
}



?>
