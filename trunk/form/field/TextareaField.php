<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';


class MXT_TextareaField extends MXT_AbstractField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function getClassAttribute()
    {
        return 'textarea-field';
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <textarea name="<?php echo $this->getKeynameWithIndex($index); ?>"><?php echo htmlentities($this->getValue($index, true)); ?></textarea>
        <?php
    }
}


?>
