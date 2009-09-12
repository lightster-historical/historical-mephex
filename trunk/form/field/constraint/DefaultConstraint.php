<?php


require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';


class MXT_DefaultConstraint implements MXT_FieldConstraint
{
    protected $defaultValue;
    protected $minCount;
    protected $maxCount;


    public function __construct($defaultValue = '', $minCount = 1, $maxCount = 1)
    {
        $this->defaultValue = $defaultValue;
        $this->minCount = $minCount;
        $this->maxCount = max($maxCount, 0);

        if($this->maxCount != 0)
            $this->minCount = min($this->minCount, $this->maxCount);
    }


    public function getDefaultFormValue()
    {
        return $this->defaultValue;
    }


    public function getMinCount()
    {
        return $this->minCount;
    }

    public function getMaxCount()
    {
        return $this->maxCount;
    }

    public function getDefaultCount()
    {
        return 1;
    }


    public function isEmpty($value)
    {
        //var_dump($value); echo is_scalar($value) ? '1' : '0'; echo '<br />';
        return is_null($value)
            || (is_array($value) && count($value) <= 0)
            || (is_scalar($value) && !is_bool($value) && trim($value) == '');
    }

    public function isValid($value)
    {
        return true;
    }

    public function parseValue($value)
    {
        return $value;
    }
}



?>
