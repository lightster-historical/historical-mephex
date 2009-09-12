<?php


require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';


interface MXT_DO_AbstractWriter
{
    public function create(MXT_DataObject $obj);
    public function update(MXT_DataObject $obj);
}



?>
