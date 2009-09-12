<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/HiddenField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/DefaultConstraint.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


class MXT_HiddenIdField extends MXT_HiddenField
{
    public function __construct($keyname, $constraint = null)
    {
        if(is_null($constraint))
            $constraint = new MXT_DefaultConstraint(0);

        parent::__construct($keyname, $constraint);
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname(), IntegerInput::getInstance());
    }
}


?>
