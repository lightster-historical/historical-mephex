<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/form/field/SetField.php';


class MXT_SetField extends MXT_AbstractField
{
    protected $pairs;


    public function __construct($keyname, MXT_FieldConstraint $constraint, $pairs)
    {
        parent::__construct($keyname, $constraint);

        $this->pairs = array();
        foreach($pairs as $pair)
        {
            if(is_null($pair))
                $this->pairs[] = null;
            else if($pair instanceof MXT_Pair)
                $this->pairs[$pair->left] = $pair;
        }
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <select name="<?php echo $this->getKeynameWithIndex($index); ?>">
        <?php
        $value = $this->parseValue($this->getValue($index, true));
        foreach($this->pairs as $pair)
        {
            if(is_null($pair))
                $pair = new MXT_Pair('', '');

            $selected = '';
            if($value instanceof MXT_Pair && $pair == $value)
                $selected = ' selected="selected"';

            ?>
             <option value="<?php echo htmlentities($pair->left); ?>"<?php echo $selected; ?>><?php echo $pair->right; ?></option>
            <?php
        }
        ?>
         </select>
        <?php
    }

    public function setValue($value, $index = 0)
    {
        if(is_array($this->pairs))
        {
            parent::setValue($value, $index);
        }
    }

    public function parseValue($value)
    {
        if($value == '' || is_null($value))
            return null;
        else if($value instanceof MXT_Pair && in_array($value, $this->pairs))
            return $value;
        else if(is_scalar($value) && array_key_exists($value, $this->pairs))
            return $this->pairs[$value];
    }
}


?>
