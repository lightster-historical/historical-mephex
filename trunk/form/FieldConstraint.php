<?php


//require_once PATH_LIB . 'com/mephex/data-object/DataField.php';


interface MXT_FieldConstraint
{
    public function getDefaultFormValue();

    public function getMinCount();
    public function getMaxCount();

    public function getDefaultCount();

    public function isValid($value);
    public function parseValue($value);
}



?>
