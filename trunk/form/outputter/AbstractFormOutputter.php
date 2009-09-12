<?php


require_once PATH_LIB . 'com/mephex/form/Form.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/fieldset/Fieldset.php';


abstract class MXT_AbstractFormOutputter
{
    public abstract function printFormAsHTML(MXT_Form $form);
    public abstract function printFieldsetAsHTML(MXT_Fieldset $fieldset);
    public abstract function printFieldAsHTML(MXT_AbstractField $field);


    public function printLegendTitle(MXT_Fieldset $fieldset)
    {
        $legendTitle = trim($fieldset->getTitle());
        if($legendTitle != '')
        {
            ?>
             <legend><?php echo $legendTitle; ?></legend>
            <?php
        }
    }
}


?>
