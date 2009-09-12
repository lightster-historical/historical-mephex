<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';


class MXT_HiddenField extends MXT_InputField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <input type="hidden" name="<?php echo $this->getKeynameWithIndex($index); ?>" value="<?php echo htmlentities($this->getValue($index, true)); ?>" />
        <?php
    }
}


?>
