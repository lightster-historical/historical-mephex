<?php


require_once PATH_LIB . 'com/mephex/form/FieldError.php';
require_once PATH_LIB . 'com/mephex/form/Form.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
//require_once PATH_LIB . 'com/mephex/form/field/HiddenField.php';
require_once PATH_LIB . 'com/mephex/form/field/SubmitField.php';
require_once PATH_LIB . 'com/mephex/form/field/TextareaField.php';
require_once PATH_LIB . 'com/mephex/form/fieldset/Fieldset.php';
require_once PATH_LIB . 'com/mephex/form/outputter/AbstractFormOutputter.php';


class MXT_DescriptiveFormOutputter extends MXT_AbstractFormOutputter
{
    public function printFormAsHTML(MXT_Form $form)
    {
        ?>
         <form action="<?php echo $form->getActionURL(); ?>" class="form-default" method="<?php echo $form->getMethod(); ?>">
        <?php
        $this->printFormErrors($form->getErrors());
        $form->printFieldsetsAsHTML($this);
        ?>
         </form>
        <?php
    }

    public function printFormErrors($errors)
    {
        if(count($errors) > 0)
        {
            ?>
             <div class="error-message">
            <?php
            $separator = '';
            foreach($errors as $error)
            {
                echo $separator;
                $this->printFormError($error);
                $separator = '<br />';
            }
            ?>
             </div>
            <?php
        }
    }

    public function printFormError(MXT_FormError $error)
    {
        echo $error->getDefaultMessage();
    }

    public function printFieldsetAsHTML(MXT_Fieldset $fieldset)
    {
        if($fieldset->getKeyname() == 'submit')
        {
            ?>
             <fieldset class="submit">
              <?php $this->printLegendTitle($fieldset); ?>
              <div class="field">
               <?php $fieldset->printFieldsAsHTML($this); ?>
              </div>
             </fieldset>
            <?php
        }
        else
        {
            ?>
             <fieldset>
            <?php
            $this->printLegendTitle($fieldset);
            $fieldset->printFieldsAsHTML($this);
            ?>
             </fieldset>
            <?php
        }
    }

    public function printFieldAsHTML(MXT_AbstractField $field)
    {
        if($field instanceof MXT_SubmitField || $field instanceof MXT_HiddenField)
        {
            $field->printFieldInputAsHTML(0);
        }
        else if($field instanceof MXT_TextareaField)
        {
            $class = $field->getClassAttribute();

            ?>
             <div class="<?php echo $class; ?>">
              <label>
               <em><?php echo $field->getTitle(); ?></em>
               <?php echo $field->getDescription(); ?>
               <?php $this->printFieldErrors($field->getErrors()); ?>
              </label>
              <?php $field->printFieldInputAsHTML(0); ?>
             </div>
            <?php
        }
        else
        {
            $constraint = $field->getConstraint();
            $class = $field->getClassAttribute();
            $values = $field->getValues();

            $minCount = $constraint->getMinCount();
            $maxCount = $constraint->getMaxCount();
            $defaultCount = max(1, $constraint->getDefaultCount());

            ?>
             <div class="<?php echo $class; ?>">
            <?php
            $count = 0;
            foreach($values as $key => $value)
            {
                if(!$constraint->isEmpty($value) && ($maxCount <= 0 || $count < $maxCount))
                {
                    $field->printFieldInputAsHTML($key);
                    $count++;
                }
            }

            $blankCount = $count + max($minCount - $count, $defaultCount);
            if($maxCount > 0)
                $blankCount = min($maxCount, $blankCount);
            for($i = $count; $i < $blankCount; $i++)
                $field->printFieldInputAsHTML($i);
            ?>
              <label>
               <em><?php echo $field->getTitle(); ?></em>
               <?php echo $field->getDescription(); ?>
               <?php $this->printFieldErrors($field->getErrors()); ?>
              </label>
             </div>
            <?php
        }
    }

    public function printFieldErrors($errors)
    {
        if(!is_null($errors))
        {
            ?>
             <span class="field-error">
            <?php
            $separator = '';
            while(!is_null($errors))
            {
                echo $separator;
                $this->printFieldError($errors);
                $separator = '<br />';

                $errors = $errors->getNext();
            }
            ?>
             </span>
            <?php
        }
    }

    public function printFieldError(MXT_FieldError $error)
    {
        echo $error->getMessage();
    }
}


?>
