<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';


class MXT_InputField extends MXT_AbstractField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <input type="text" name="<?php echo $this->getKeynameWithIndex($index); ?>" value="<?php echo htmlentities($this->getValue($index, true)); ?>" />
        <?php
    }
}


?>
