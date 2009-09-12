<?php


require_once PATH_LIB . 'com/mephex/data-object/class/AbstractDatabaseDataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/io/DatabaseWriter.php';


class MXT_DO_DatabaseDataClassWriter extends MXT_DO_DatabaseWriter
{
    public function __construct(MXT_AbstractDatabaseDataClass $class)
    {
        parent::__construct($class, $class);
    }
}



?>
