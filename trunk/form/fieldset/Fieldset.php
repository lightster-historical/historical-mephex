<?php


require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/outputter/AbstractFormOutputter.php';


class MXT_Fieldset
{
    protected $form;

    private $keyname;

    protected $hasErrors;

    protected $fields;
    protected $fieldsByKeyname;


    public function __construct($keyname, $fields = array())
    {
        $this->form = null;

        $this->keyname = $keyname;

        $this->hasErrors = null;

        $this->fields = array();
        $this->fieldsByKeyname = array();

        if(is_array($fields))
        {
            foreach($fields as $field)
            {
                $this->addField($field);
            }
        }
    }


    public function addField(MXT_AbstractField $field)
    {
        $form = $this->getForm();
        if(!is_null($form))
            $form->addFieldShortcut($field);

        $this->fields[] = $field;
        $this->fieldsByKeyname[$field->getKeyname()] = $field;

        $field->setFieldset($this);
    }

    public function removeField(MXT_AbstractField $field)
    {
        $key = array_search($field, $this->fields);
        if(!($key === false))
        {
            $field->setFieldset(null);

            unset($this->fields[$key]);
            unset($this->fieldsByKeyname[$field->getKeyname()]);
        }
    }


    public function getForm()
    {
        return $this->form;
    }

    public final function getKeyname()
    {
        return $this->keyname;
    }

    public function getTitle()
    {
        return MXT_Language::getStatement($this->getKeyname() . '.title');
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldCount()
    {
        return count($this->fields);
    }

    public function getField($index)
    {
        if(0 <= $index && $index < $this->getFieldCount())
            return $this->fields[$index];

        return null;
    }

    public function getFieldByKeyname($keyname)
    {
        if(array_key_exists($keyname, $this->fieldsByKeyname))
            return $this->fieldsByKeyname[$keyname];

        return null;
    }


    public function setForm(MXT_Form $form = null)
    {
        if(!($this->form === $form))
        {
            if(!is_null($this->form))
            {
                $temp = $this->form;
                $this->form = null;
                $temp->removeFieldset($this);
            }

            $this->form = $form;
        }
    }

    public function setInputs(Input $input)
    {
        foreach($this->fields as $field)
        {
            $field->setInputs($input);
        }
    }

    public function setValuesUsingInput(Input $input)
    {
        foreach($this->fields as $field)
            $field->setValuesUsingInput($input);
    }

    public function validate()
    {
        $hasErrors = false;
        foreach($this->fields as $field)
        {
            $hasErrors = (!$field->validateField()) || $hasErrors;
        }

        $this->hasErrors = $hasErrors;

        return !$this->hasErrors;
    }

    public function notifyOfFormErrors()
    {
        foreach($this->fields as $field)
            $field->notifyOfFormErrors();
    }


    public function printFieldsAsHTML(MXT_AbstractFormOutputter $outputter)
    {
        $fields = $this->getFields();
        foreach($fields as $field)
        {
            $outputter->printFieldAsHTML($field);
        }
    }
}


?>
