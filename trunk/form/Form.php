<?php


require_once PATH_LIB . 'com/mephex/form/FormError.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/fieldset/Fieldset.php';
require_once PATH_LIB . 'com/mephex/form/outputter/AbstractFormOutputter.php';
require_once PATH_LIB . 'com/mephex/language/Language.php';
require_once PATH_LIB . 'com/mephex/util/context/AbstractContextable.php';
require_once PATH_LIB . 'com/mephex/util/context/DefaultContext.php';


class MXT_Form implements MXT_AbstractContextable
{
    protected $actionURL;

    protected $fieldsets;
    protected $fieldsetsByKeyname;
    protected $currentFieldset;

    protected $fieldsByKeyname;

    protected $method;

    protected $errors;
    protected $hasErrors;

    protected $context;


    public function __construct($actionURL, $method = 'post')
    {
        $this->actionURL = $actionURL;

        $this->fieldsets = null;
        $this->fieldsetsByKeyname = null;
        $this->currentFieldset = null;

        $this->fieldsByKeyname = null;

        $this->method = strtolower($method);

        $this->errors = array();
        $this->hasErrors = null;

        $this->context = null;
    }


    protected function initFields()
    {
        $this->fieldsets = array();
        $this->fieldsetsByKeyname = array();

        $this->fieldsByKeyname = array();
    }


    public function addFieldset(MXT_Fieldset $fieldset)
    {
        if(!is_array($this->fieldsets))
            $this->fieldsets = array();

        $this->fieldsets[] = $fieldset;
        $this->fieldsetsByKeyname[$fieldset->getKeyname()] = $fieldset;
        $this->currentFieldset = $fieldset;

        $fieldset->setForm($this);
    }

    public function addSimpleFieldset($keyname)
    {
        $fieldset = new MXT_Fieldset($keyname);
        $this->addFieldset($fieldset);
    }

    public function removeFieldset(MXT_Fieldset $fieldset)
    {
        $key = array_search($fieldset, $this->getFieldsets());
        if(!($key === false))
        {
            $this->fieldset->setForm(null);

            unset($this->fieldsets[$key]);
            unset($this->fieldsets[$fieldset->getKeyname()]);
        }
    }


    //*
    public function addField(MXT_AbstractField $field)
    {
        if(is_null($this->currentFieldset))
            $this->addSimpleFieldset('basic');

        $this->currentFieldset->addField($field);
    }

    public function addFieldShortcut(MXT_AbstractField $field, $keyname = null)
    {
        if(!is_array($this->fieldsByKeyname))
            $this->fieldsByKeyname = array();
        if(is_null($keyname))
            $keyname = $field->getKeyname();

        $this->fieldsByKeyname[$keyname] = $field;
    }
    //*/


    public function getActionURL()
    {
        return $this->actionURL;
    }

    public function getFieldsets()
    {
        if(is_null($this->fieldsets))
            $this->initFields();

        return $this->fieldsets;
    }

    public function getFieldsetsByKeyname()
    {
        if(is_null($this->fieldsetsByKeyname))
            $this->initFields();

        return $this->fieldsetsByKeyname;
    }


    public function getFieldsetCount()
    {
        return count($this->getFieldsets);
    }

    public function getFieldset($index)
    {
        $fieldsets = $this->getFieldsets();

        if(0 <= $index && $index < $this->getFieldsetCount())
            return $fieldsets[$index];

        return null;
    }

    public function getFieldsetUsingKeyname($keyname)
    {
        $fieldsets = $this->getFieldsetsByKeyname();

        if(array_key_exists($keyname, $fieldsets))
            return $fieldsets[$keyname];

        return null;
    }

    public function getMethod()
    {
        return $this->method;
    }


    public function getField($fieldsetKeyname, $fieldKeyname)
    {
        if(array_key_exists($fieldsetKeyname, $this->fieldsetsByKeyname))
            return $this->fieldsetsByKeyname[$fieldsetKeyname]->getFieldByKeyname($fieldKeyname);

        return null;
    }

    public function getValue($fieldsetKeyname, $fieldKeyname, $scalar = 0)
    {
        $field = $this->getField($fieldsetKeyname, $fieldKeyname);
        if(!is_null($field))
            return $field->getValue();

        return null;
    }

    public function getFieldUsingShortcut($keyname)
    {
        $fields = $this->getFields();
        if(array_key_exists($keyname, $fields))
            return $fields[$keyname];

        return null;
    }

    public function getValueShortcut($keyname, $scalar = false)
    {
        $field = $this->getFieldUsingShortcut($keyname);
        if(!is_null($field))
            return $field->getValue();

        return null;
    }

    public function getFields()
    {
        if(is_null($this->fieldsByKeyname))
            $this->initFields();

        return $this->fieldsByKeyname;
    }


    public function isSubmitted(Input $input = null)
    {
        if(($this->getMethod() == 'post'
            && strtolower($_SERVER['REQUEST_METHOD']) == 'post'))
        {
            if(!is_null($input))
            {
                $this->setValuesUsingInput($input);
            }

            return true;
        }

        return null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors(Input $input = null)
    {
        if(!is_null($input))
        {
            $this->validateUsingInput($input);

            if(count($this->getErrors()) > 0)
                $this->hasErrors = true;
        }

        return $this->hasErrors;
    }


    public function addError(MXT_FormError $error)
    {
        return $this->errors[$error->getIndex()] = $error;
    }


    public function setInputs(Input $input)
    {
        $fieldsets = $this->getFieldsets();

        foreach($fieldsets as $fieldset)
            $fieldset->setInputs($input);
    }

    public function setValuesUsingInput(Input $input)
    {
        $fieldsets = $this->getFieldsets();

        foreach($fieldsets as $fieldset)
            $fieldset->setValuesUsingInput($input);
    }

    public function validate()
    {
        $fieldsets = $this->getFieldsets();

        $hasErrors = false;
        foreach($fieldsets as $fieldset)
        {
            $hasErrors = (!$fieldset->validate()) || $hasErrors;
        }

        if($hasErrors)
        {
            foreach($fieldsets as $fieldset)
                $fieldset->notifyOfFormErrors();
        }

        $this->hasErrors = $hasErrors;

        return !$this->hasErrors;
    }


    public function printFormAsHTML(MXT_AbstractFormOutputter $outputter)
    {
        $outputter->printFormAsHTML($this);
    }

    public function printFieldsetsAsHTML(MXT_AbstractFormOutputter $outputter)
    {
        $fieldsets = $this->getFieldsets();

        foreach($fieldsets as $fieldset)
        {
            $outputter->printFieldsetAsHTML($fieldset);
        }
    }


    public function getContext()
    {
        if(is_null($this->context))
            return MXT_DefaultContext::getDefaultContext();
        else
            return $this->context;
    }
}


?>
