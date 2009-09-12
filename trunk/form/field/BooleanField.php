<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/DefaultConstraint.php';


class MXT_BooleanField extends MXT_AbstractField
{
    protected $trueLabel;
    protected $falseLabel;

    public function __construct($keyname, $constraint = null)
    {
        if(is_null($constraint))
            $constraint = new MXT_DefaultConstraint(false);

        parent::__construct($keyname, $constraint);
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <label for="<?php echo $this->getKeyname(). $index; ?>-1" class="true-label">
          <?php echo $this->getStatement('true'); ?>
          <input type="radio" name="<?php echo $this->getKeynameWithIndex($index); ?>" id="<?php echo $this->getKeyname() . $index; ?>-1" value="1"<?php echo $this->getValue($index, true) ? ' checked="checked"' : ''; ?> />
         </label>
         <label for="<?php echo $this->getKeyname(). $index; ?>-0" class="false-label">
          <input type="radio" name="<?php echo $this->getKeynameWithIndex($index); ?>" id="<?php echo $this->getKeyname() . $index; ?>-0" value="0"<?php echo !$this->getValue($index, true) ? ' checked="checked"' : ''; ?> />
          <?php echo $this->getStatement('false'); ?>
         </label>
        <?php
    }

    public function parseValue($value)
    {
        if($value == '' || $value == '0')
            return false;
        else
            return true;
    }
}


?>
