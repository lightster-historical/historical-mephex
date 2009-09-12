<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/Pair.php';
require_once PATH_LIB . 'com/mephex/form/field/DateField.php';


class MXT_TimeField extends MXT_AbstractField
{
    protected $dateField;
    protected $isUpdating;


    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);

        $this->dateField = null;
        $this->isUpdating = false;
    }


    public function setDateField(MXT_DateField $field)
    {
        $this->dateField = $field;
        if($field->getTimeField() != $this)
            $field->setTimeField($this);
    }

    public function getDateField()
    {
        return $this->dateField;
    }


    public function isUpdating()
    {
        return $this->isUpdating;
    }

    public function beginUpdating()
    {
        $this->isUpdating = true;
    }

    public function endUpdating()
    {
        $this->isUpdating = false;
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname() . '_hour')
            && $input->set($this->getKeyname() . '_minute')
            && $input->set($this->getKeyname() . '_pm');
    }


    public function printFieldInputAsHTML($index = null)
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

        ?>
         <style type="text/css">
          .time-field
          {
              border: 1px solid #666666;
              display: inline-block;
              padding: 3px 3px 3px 4px;
          }

          .time-field input.hour,
          .time-field input.minute
          {
              border: solid #999999;
              border-width: 0 0 1px 0;
              float: none;
              clear: none;
              width: 1.5em;
          }

          .time-field input.ampm
          {
              margin: 0;
          }

          .time-field label#ampm-label
          {
              display: inline;
              clear: none;
              margin: 0;
              padding: 2px 1px 4px 1px;
              width: 200px;
              border: 1px #999999 solid;
              background: #eeeeee;
              font-size: .8em;
              font-weight: bold;
          }

          .time-field label
          {
          }
         </style>
         <div class="time-field">
          <input type="text" name="" class="hour" id="month" value="<?php printf('%02s', $currH); ?>" maxlength="2" /> :
          <input type="text" name="" class="minute" id="day" value="<?php printf('%02s', $currM); ?>" maxlength="2" />
          <label id="ampm-label" for="ampm"><input type="checkbox" name="" class="ampm" id="ampm" value="1" maxlength="4" />PM</label>
         </div>
         <!--
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
         //-->
        <?php
    }

    public function setValue($value, $index = 0)
    {
        if($value instanceof Date || is_null($value))
        {
            parent::setValue($value, $index);
        }
    }


    // TimeField->setValuesUsingInput($input):
    //   - if there is an associated DateField and this TimeField is not
    //     currently in the middle of an update
    //     - record that an update is ongoing
    //     - let the DateField do the updating
    //     - record that an update is NOT ongoing
    //   - else
    //     - for each time inputted
    //       - retrieve the individual inputs
    //       - create a Date object using the inputted time and 1 Jan. 2009
    //         as the date
    //       - store the Date object in the TimeField using the key

    // DateField->setValuesUsingInput($input):
    //   - if there is an associated TimeField
    //     - tell the TimeField that an update is ongoing
    //     - tell the TimeField do to do its updating
    //     - tell the TimeField that an update is NOT ongoing
    //   - for each date inputted
    //     - retrieve the individual inputs
    //     - if there is an associated TimeField
    //       - create a Date object using the inputted date and the time
    //         (with the key that matches the date key) from the TimeField
    //       - store the Date object in the TimeField
    //     - else
    //       - create a Date object using the inputted date and
    //         00:00:00 as the time
    //     - store the Date object in the DateField using the key


    public function setValuesUsingInput(Input $input)
    {
        $dateField = $this->getDateField();
        if(!is_null($dateField) && !$this->isUpdating())
        {
            $this->beginUpdating();
            $dateField->setValuesUsingInput($input);
            $this->endUpdating();
        }
        else
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

                if(($pm == 1 && $hour != 12) || ($pm != 1 && $hour == 12))
                    $hour += 12;

                if($hour != '' && $minute != '')
                    $value = new Date(2009, 1, 1, $hour, $minute, 0, $timezone);
                else
                    $value = null;

                $this->setValue($value, $key);
            }

            $this->endUpdating();
        }
    }
}


?>
