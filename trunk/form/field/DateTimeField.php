<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/form/field/DateField.php';
require_once PATH_LIB . 'com/mephex/form/field/TimeField.php';


class MXT_DateTimeField extends MXT_AbstractField
{
    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function setFieldset(MXT_Fieldset $fieldset = null)
    {
        parent::setFieldset($fieldset);
        //$this->dateField->setFieldset($fieldset);
        //$this->timeField->setFieldset($fieldset);
    }


    public function setInputs(Input $input)
    {
        $date = $time = true;

        if(!($this instanceof MXT_TimeField))
            $date = $this->setDateInputs($input);
        if(!($this instanceof MXT_DateField))
            $time = $this->setTimeInputs($input);

        return $date && $time;
        //return $this->dateField->setInputs($input)
        //    && $this->timeField->setInputs($input);
    }


    public function printFieldInputAsHTML($index = null)
    {
        echo '<div class="datetime-field">';

        if(!($this instanceof MXT_TimeField))
            $this->printDateFieldInputAsHTML($index);
        if(!($this instanceof MXT_DateField))
            $this->printTimeFieldInputAsHTML($index);

        echo '</div>';

        //$this->dateField->printFieldInputAsHTML($index);
        //$this->timeField->printFieldInputAsHTML($index);
    }

    public function setValue($value, $index = 0)
    {
        if($value instanceof Date || is_null($value))
        {
            /*
            if(!is_null($this->dateField))
                $this->dateField->setValue($value, $index);
            if(!is_null($this->timeField))
                $this->timeField->setValue($value, $index);
            */

            parent::setValue($value, $index);
        }
    }

    public function setValuesUsingInput(Input $input)
    {
        if(!($this instanceof MXT_TimeField))
            $date = $this->setDateValuesUsingInput($input);
        if(!($this instanceof MXT_DateField))
            $time = $this->setTimeValuesUsingInput($input);

        /*
        $this->dateField->setValuesUsingInput($input);
        $this->timeField->setValuesUsingInput($input);

        $values = $this->dateField->getValues();
        foreach($values as $key => $value)
        {
            $date = $this->dateField->getValue($key);
            $time = $this->timeField->getValue($key);

            $timezone = $this->getForm()->getContext()->getValueOrDefault('timezone', 0);
            if(!is_null($date) && !is_null($time))
                $value = new Date($date->format('q ') . $time->format('Q'), $timezone);
            else
                $value = null;

            $this->setValue($value, $key);
        }
        */
    }



    protected function setDateInputs(Input $input)
    {
        return $input->set($this->getKeyname() . '_year')
            && $input->set($this->getKeyname() . '_month')
            && $input->set($this->getKeyname() . '_date');
    }

    protected function setTimeInputs(Input $input)
    {
        return $input->set($this->getKeyname() . '_hour')
            && $input->set($this->getKeyname() . '_minute')
            && $input->set($this->getKeyname() . '_pm');
    }


    public function setDateValue($dateValue, $index = 0)
    {
        $timeValue = $this->getValue($index);

        if(is_null($dateValue) || is_null($timeValue))
            $this->setValue($dateValue, $index);
        else
            $this->setValue(new Date($dateValue, $timeValue), $index);
    }

    public function setTimeValue($timeValue, $index = 0)
    {
        $dateValue = $this->getValue($index);

        if(is_null($dateValue) || is_null($timeValue))
            $this->setValue($dateValue, $index);
        else
            $this->setValue(new Date($dateValue, $timeValue), $index);
    }


    public function printDateFieldInputAsHTML($index = null)
    {
        $value = $this->getValue($index, true);
        if($value instanceof Date)
        {
            $context = $this->getForm()->getContext();

            $timezone = 0;
            if(!($this instanceof MXT_DateField))
            {
                $timezone = $context->getValueOrDefault('timezone', 0);
            }

            $currY = $value->format('Y', $timezone);
            $currM = $value->format('n', $timezone);
            $currD = $value->format('j', $timezone);
        }
        else
        {
            $currY = '';
            $currM = '';
            $currD = '';
        }

        ?>
         <div class="date-field">
          <input type="text" name="<?php echo $this->getKeyname(); ?>_month<?php echo $this->getFieldIndex($index); ?>" class="month" value="<?php printf('%02s', $currM); ?>" maxlength="2" /> /
          <input type="text" name="<?php echo $this->getKeyname(); ?>_date<?php echo $this->getFieldIndex($index); ?>" class="date" value="<?php printf('%02s', $currD); ?>" maxlength="2" /> /
          <input type="text" name="<?php echo $this->getKeyname(); ?>_year<?php echo $this->getFieldIndex($index); ?>" class="year" value="<?php printf('%04s', $currY); ?>" maxlength="4" />
         </div>
        <?php
        /*
        ?>
         <select name="<?php echo $this->getKeyname(); ?>_month<?php echo $this->getFieldIndex($index); ?>" style="border: 0;">
          <option value=""></option>
        <?php
        for($m = 1; $m <= 12; $m++)
        {
            $selected = '';
            if($currM == $m)
                $selected = ' selected="selected"';

            $date = new Date(2009, $m, 1);

            ?>
             <option value="<?php echo $m; ?>"<?php echo $selected; ?>><?php echo $date->format('M'); ?></option>
            <?php
        }
        ?>
         </select>
         <select name="<?php echo $this->getKeyname(); ?>_date<?php echo $this->getFieldIndex($index); ?>" class="multiple">
          <option value=""></option>
        <?php
        for($d = 1; $d <= 31; $d++)
        {
            $selected = '';
            if($currD == $d)
                $selected = ' selected="selected"';

            ?>
             <option value="<?php echo $d; ?>"<?php echo $selected; ?>><?php echo $d; ?></option>
            <?php
        }
        ?>
         </select>
         <select name="<?php echo $this->getKeyname(); ?>_year<?php echo $this->getFieldIndex($index); ?>" class="multiple">
          <option value=""></option>
        <?php
        for($y = Date::now('Y') + 5; $y >= 1970; $y--)
        {
            $selected = '';
            if($currY == $y)
                $selected = ' selected="selected"';

            ?>
             <option value="<?php echo $y; ?>"<?php echo $selected; ?>><?php echo $y; ?></option>
            <?php
        }
        ?>
         </select>
        <?php
        */
    }

    public function printTimeFieldInputAsHTML($index = null)
    {
        $value = $this->getValue($index, true);
        if($value instanceof Date)
        {
            $context = $this->getForm()->getContext();
            $timezone = $context->getValueOrDefault('timezone', 0);

            $currH = $value->format('g', $timezone);
            $currM = $value->format('i', $timezone);
            $currPm = $value->format('a', $timezone) == 'pm';
        }
        else
        {
            $currH = '';
            $currM = '';
            $currPm = false;
        }

        $checked = '';
        if($currPm)
            $checked = ' checked="checked"';

        ?>
         <div class="time-field">
          <input type="text" name="<?php echo $this->getKeyname(); ?>_hour<?php echo $this->getFieldIndex($index); ?>" class="hour" value="<?php printf('%02s', $currH); ?>" maxlength="2" />:<input type="text" name="<?php echo $this->getKeyname(); ?>_minute<?php echo $this->getFieldIndex($index); ?>" class="minute" value="<?php printf('%02s', $currM); ?>" maxlength="2" />
          <label class="ampm" for="<?php echo $this->getKeyname(); ?>_pm<?php echo $this->getFieldIndex($index); ?>"><input type="checkbox" name="<?php echo $this->getKeyname(); ?>_pm<?php echo $this->getFieldIndex($index); ?>" class="ampm" id="<?php echo $this->getKeyname(); ?>_pm<?php echo $this->getFieldIndex($index); ?>"<?php echo $checked; ?> value="1" maxlength="4" />PM</label>
         </div>
        <?php
        /*
        ?>
         <select name="<?php echo $this->getKeyname(); ?>_hour<?php echo $this->getFieldIndex($index); ?>">
          <option value=""></option>
        <?php
        for($h = 1; $h <= 12; $h++)
        {
            $selected = '';
            if($currH == $h)
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
            if($currM == $min)
                $selected = ' selected="selected"';
            ?>
             <option value="<?php echo $min; ?>"<?php echo $selected; ?>><?php echo $min; ?></option>
            <?php
        }
        ?>
         </select>
         <select name="<?php echo $this->getKeyname(); ?>_pm<?php echo $this->getFieldIndex($index); ?>" class="multiple">
          <option value="0"<?php echo (!$currPm) ? ' selected="selected"' : ''; ?>>am</option>
          <option value="1"<?php echo $currPm ? ' selected="selected"' : ''; ?>>pm</option>
         </select>
        <?php
        */
    }


    protected function setDateValuesUsingInput(Input $input)
    {
        $context = $this->getFieldset()->getForm()->getContext();

        $years = $input->get($this->getKeyname() . '_year');
        $months = $input->get($this->getKeyname() . '_month');
        $dates = $input->get($this->getKeyname() . '_date');

        if(!is_array($years))
            $years = array($years);
        if(!is_array($months))
            $months = array($months);
        if(!is_array($dates))
            $dates = array($dates);

        foreach($years as $key => $year)
        {
            $year = $years[$key];
            $month = $months[$key];
            $date = $dates[$key];

            if($year != '' && $month != '' && $date != '')
                $value = new Date($year, $month, $date, 0, 0, 0, 0);
            else
                $value = null;

            $this->setDateValue($value, $key);
        }
    }

    protected function setTimeValuesUsingInput(Input $input)
    {
        $context = $this->getFieldset()->getForm()->getContext();
        $timezone = $context->getValueOrDefault('timezone', 0);

        $hours = $input->get($this->getKeyname() . '_hour');
        $minutes = $input->get($this->getKeyname() . '_minute');
        $pms = $input->get($this->getKeyname() . '_pm');

        if(!is_array($hours))
            $hours = array($hours);
        if(!is_array($minutes))
            $minutes = array($minutes);
        if(!is_array($pms))
            $pms = array($pms);

        foreach($hours as $key => $hour)
        {
            $hour = $hours[$key];
            $minute = $minutes[$key];
            $pm = $pms[$key];

            if(($hour != '0' && !($hour >= 13)) &&
                (($pm == '1' && $hour != 12) || ($pm != '1' && $hour == 12)))
                $hour += 12;

            if($hour != '' && $minute != '')
                $value = new Date(2009, 1, 1, $hour, $minute, 0, $timezone);
            else
                $value = null;

            $this->setTimeValue($value, $key);
        }
    }
}


?>
