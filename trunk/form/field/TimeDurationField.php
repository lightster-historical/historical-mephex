<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/core/TimeDuration.php';
require_once PATH_LIB . 'com/mephex/form/field/SetField.php';


class MXT_TimeDurationField extends MXT_AbstractField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname() . '_hour')
            && $input->set($this->getKeyname() . '_minute')
            && $input->set($this->getKeyname() . '_second');
    }


    public function printFieldInputAsHTML($index = null)
    {
        $value = $this->getValue($index, true);
        if($value instanceof MXT_TimeDuration)
        {
            $currH = $value->getHour();
            $currM = $value->getMinute();
            $currS = $value->getSecond();
        }
        else
        {
            $currH = null;
            $currM = null;
            $currS = null;
        }

        ?>
         <select name="<?php echo $this->getKeyname(); ?>_hour<?php echo $this->getFieldIndex($index); ?>">
          <option value=""></option>
        <?php
        for($h = 1; $h <= 500; $h++)
        {
            $selected = '';
            if(!is_null($currH) && $currH == $h)
                $selected = ' selected="selected"';

            ?>
             <option value="<?php echo $h; ?>"<?php echo $selected; ?>><?php echo $h; ?></option>
            <?php
        }
        ?>
         </select>
         <select name="<?php echo $this->getKeyname(); ?>_minute<?php echo $this->getFieldIndex($index); ?>" class="multiple">
          <option value=""></option>
        <?php
        for($m = 0; $m <= 59; $m++)
        {
            $min = sprintf('%02d', $m);

            $selected = '';
            if(!is_null($currM) && $currM == $m)
                $selected = ' selected="selected"';
            ?>
             <option value="<?php echo $m; ?>"<?php echo $selected; ?>><?php echo $min; ?></option>
            <?php
        }
        ?>
         </select>
         <select name="<?php echo $this->getKeyname(); ?>_second<?php echo $this->getFieldIndex($index); ?>" class="multiple">
          <option value=""></option>
        <?php
        for($m = 0; $m <= 59; $m++)
        {
            $min = sprintf('%02d', $m);

            $selected = '';
            if(!is_null($currS) && $currS == $m)
                $selected = ' selected="selected"';
            ?>
             <option value="<?php echo $m; ?>"<?php echo $selected; ?>><?php echo $min; ?></option>
            <?php
        }
        ?>
         </select>
        <?php
    }

    public function setValue($value, $index = 0)
    {
        if($value instanceof MXT_TimeDuration || is_null($value))
        {
            parent::setValue($value, $index = 0);
        }
    }

    public function setValuesUsingInput(Input $input)
    {
        $hours = $input->get($this->getKeyname() . '_hour');
        $minutes = $input->get($this->getKeyname() . '_minute');
        $seconds = $input->get($this->getKeyname() . '_second');

        if(!is_array($hours))
            $hours = array($hours);
        if(!is_array($minutes))
            $minutes = array($minutes);
        if(!is_array($seconds))
            $seconds = array($seconds);

        foreach($hours as $key => $hour)
        {
            $hour = intval($hours[$key]);
            $minute = $minutes[$key];
            $second = $seconds[$key];

            if($minute != '' && $second != '')
                $value = new MXT_TimeDuration($hour, $minute, $second);
            else
                $value = null;

            $this->setValue($value, $key);
        }
    }
}


?>
