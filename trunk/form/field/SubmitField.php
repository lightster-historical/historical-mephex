<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/SubmitConstraint.php';


class MXT_SubmitField extends MXT_AbstractField
{
    protected $isNameOutput;


    public function __construct($keyname)
    {
        parent::__construct($keyname, new MXT_SubmitConstraint());
        $this->isNameOutput = true;
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        if($this->isNameOutput())
            $name = ' name="' . $this->getKeynameWithIndex($index) . '"';
        else
            $name = '';

        ?>
         <input type="submit"<?php echo $name; ?> value="<?php echo htmlentities($this->getTitle()); ?>" />
        <?php
    }


    public function enableNameOutput($enable)
    {
        $this->isNameOutput = $enable;
    }

    public function isNameOutput()
    {
        return $this->isNameOutput;
    }
}


?>
