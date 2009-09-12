<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/form/field/DateTimeField.php';


class MXT_DateField extends MXT_DateTimeField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }
}


?>
