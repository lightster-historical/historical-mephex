<?php


require_once PATH_LIB . 'com/mephex/form/FieldError.php';
require_once PATH_LIB . 'com/mephex/form/Form.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
//require_once PATH_LIB . 'com/mephex/form/field/HiddenField.php';
require_once PATH_LIB . 'com/mephex/form/field/SubmitField.php';
require_once PATH_LIB . 'com/mephex/form/field/TextareaField.php';
require_once PATH_LIB . 'com/mephex/form/fieldset/Fieldset.php';
require_once PATH_LIB . 'com/mephex/form/outputter/AbstractFormOutputter.php';


class MXT_DO_DefaultFilterFormOutputter extends MXT_AbstractFormOutputter
{
    public function printFormAsHTML(MXT_Form $form)
    {
        ?>
         <form action="<?php echo $form->getActionURL(); ?>" class="form-filter-default" method="<?php echo $form->getMethod(); ?>">
        <?php
        $form->printFieldsetsAsHTML($this);
        ?>
         </form>
        <?php
    }

    public function printFieldsetAsHTML(MXT_Fieldset $fieldset)
    {
        $fieldset->printFieldsAsHTML($this);
    }

    public function printFieldAsHTML(MXT_AbstractField $field)
    {
        if($field instanceof MXT_SubmitField)
        {
            ?>
              <dl>
               <dt>&nbsp;</dt>
               <dd><?php echo $field->printFieldInputAsHTML(); ?></dd>
              </dl>
            <?php
        }
        else
        {
            ?>
              <dl>
               <dt><?php echo $field->getTitle(); ?></dt>
               <dd><?php echo $field->printFieldInputAsHTML(); ?></dd>
              </dl>
            <?php
        }
    }
}


?>
