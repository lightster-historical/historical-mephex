<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Range.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


class MXT_IntegerField extends MXT_InputField
{
    const ERROR_OUT_OF_RANGE = 1;

    protected $range;


    public function __construct($keyname, MXT_FieldConstraint $constraint, MXT_Range $range = null)
    {
        parent::__construct($keyname, $constraint);

        $this->range = $range;
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function parseValue($value)
    {
        if(trim($value) != '')
            $value = IntegerInput::getInstance()->parseValue($value);

        return $value;
    }

    public function validateValueUsingIndex($index)
    {
        if($this->getValue($index).'' != '' && !is_null($this->range)
            && ($this->range->getMin() > $this->getValue($index)
            || $this->getValue($index) > $this->range->getMax()))
        {
            throw new MXT_FieldError(self::ERROR_OUT_OF_RANGE, $this->getErrorStatement('integer', 'out-of-range'));
            //throw new MXT_FieldError(self::ERROR_OUT_OF_RANGE, 'The value is not within the allowable range (' . $this->range->getMin() . ' - ' . $this->range->getMax() . ')');

            return false;
        }

        return true;
    }
}


?>
