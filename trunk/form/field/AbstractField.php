<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/FieldConstraint.php';
require_once PATH_LIB . 'com/mephex/form/FieldError.php';
require_once PATH_LIB . 'com/mephex/form/FieldParser.php';
require_once PATH_LIB . 'com/mephex/form/FieldValidator.php';
require_once PATH_LIB . 'com/mephex/util/context/DefaultContext.php';


abstract class MXT_AbstractField
{
    const ERROR_TOO_MANY = -1;
    const ERROR_TOO_FEW = -2;
    const ERROR_CONFIRM_MISMATCH = -3;


    protected $fieldset;

    protected $keyname;
    protected $constraint;

    protected $confirmable;
    protected $confirmation;
    protected $confirmField;

    protected $values;

    protected $errors;


    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        $this->fieldset = null;

        $this->keyname = $keyname;
        $this->constraint = $constraint;

        $this->values = array();

        $this->confirmable = false;
        $this->confirmation = false;
        $this->confirmField = null;

        $this->errors = null;
    }


    public function getKeyname()
    {
        return $this->keyname;
    }


    public function getConstraint()
    {
        return $this->constraint;
    }

    public function setConstraint(MXT_FieldConstraint $constraint)
    {
        $this->constraint = $constraint;
        $this->values = array();
    }


    public function getIdAttribute()
    {
        return null;
    }

    public function getClassAttribute()
    {
        return 'field';
    }


    public function getStatement($subKey, $nullOnMissing = false)
    {
        $key = $this->getKeyname() . '.' . $subKey;
        $statement = MXT_Language::getStatement($key, $nullOnMissing);

        return $statement;
    }

    public function getTitle()
    {
        return $this->getStatement('title');
    }

    public function getDescription()
    {
        return $this->getStatement('description');
    }


    public function getForm()
    {
        $fieldset = $this->getFieldset();
        if(!is_null($fieldset))
            return $fieldset->getForm();

        return null;
    }

    public function getFieldset()
    {
        return $this->fieldset;
    }

    public function setFieldset(MXT_Fieldset $fieldset = null)
    {
        if(!($this->fieldset === $fieldset))
        {
            if(!is_null($this->fieldset))
            {
                $temp = $this->fieldset;
                $this->fieldset = null;
                $temp->removeField($this);
            }

            $this->fieldset = $fieldset;
        }
    }


    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !is_null($this->getErrors());
    }

    public function addError(MXT_FieldError $error)
    {
        if(is_null($this->errors))
            $this->errors = $error;
        else
            $this->errors->addError($error);
    }


    public function isConfirmable()
    {
        return $this->confirmable;
    }

    public function isConfirmation()
    {
        return $this->confirmation;
    }

    protected function getConfirmField()
    {
        return $this->confirmField;
    }

    public function makeConfirmableBy(MXT_AbstractField $otherField)
    {
        $this->confirmable = true;
        $otherField->confirmation = true;

        $this->confirmField = $otherField;
        $otherField->confirmField = $this;

        return $this;
    }


    public function setInputs(Input $input)
    {
        return true;
    }


    public abstract function printFieldInputAsHTML($index = null);

    public function getKeynameWithIndex($index)
    {
        if(is_null($index))
            return $this->getKeyname();
        else
            return $this->getKeyname() . $this->getFieldIndex($index);
    }

    public function getFieldIndex($index)
    {
        $constraint = $this->getConstraint();
        if(is_null($index) || $constraint->getMaxCount() == 1)
            return '';
        else
            return "[$index]";
    }


    public function getValue($index = 0, $defaultOnMissing = false)
    {
        if(is_array($this->values))
        {
            if(is_null($index))
                $index = 0;

            if(array_key_exists($index, $this->values))
                return $this->values[$index];
            else if($defaultOnMissing)
                return $this->getDefaultFormValue();
        }

        return null;
    }

    public function getDefaultFormValue()
    {
        $constraint = $this->getConstraint();
        if(!is_null($constraint))
            return $this->getConstraint()->getDefaultFormValue();

        return null;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function addValue($value)
    {
        $constraint = $this->getConstraint();
        $value = $constraint->parseValue($value);
        if(!$constraint->isEmpty($value))
            $this->values[] = $value;
    }

    public function setValue($value, $index = 0)
    {
        $constraint = $this->getConstraint();
        $value = $this->parseValue($value);
        $value = $constraint->parseValue($value);
        $this->values[$index] = $value;
    }

    public function setValues($values)
    {
        if(is_array($values))
        {
            foreach($values as $key => $value)
                $this->setValue($value, $key);
        }
        else
            $this->setValue($values, 0);
    }

    public function setValuesUsingInput(Input $input)
    {
        $values = $input->get($this->getKeyname());
        $this->setValues($values);
    }


    public function validateField()
    {
        try {$this->validateCount();}
        catch(MXT_FieldError $ex)
            {$this->addError($ex);}

        $this->validateUsingValidator();
        $this->validateValues();

        try {$this->validateConfirm();}
        catch(MXT_FieldError $ex)
            {$this->addError($ex);}

        return !$this->hasErrors();
    }

    protected function validateCount()
    {
        $constraint = $this->getConstraint();

        $values = $this->getValues();
        $valueCount = 0;
        foreach($values as $value)
        {
            if(!$constraint->isEmpty($value))
                $valueCount++;
        }

        $minCount = max($constraint->getMinCount(), 0);
        $maxCount = $constraint->getMaxCount();

        if($valueCount < $minCount)
            throw new MXT_FieldError(self::ERROR_TOO_FEW, $this->getErrorStatement('abstract', 'too-few'));
        else if($maxCount > 0 && $maxCount < $valueCount)
            throw new MXT_FieldError(self::ERROR_TOO_MANY, $this->getErrorStatement('abstract', 'too-many'));
    }

    protected function validateUsingValidator()
    {
        if(!$this->hasErrors())
        {
            $constraint = $this->getConstraint();

            $values = $this->getValues();
            foreach($values as $value)
            {
                try {$constraint->isValid($value);}
                catch(MXT_FieldError $ex)
                    {$this->addError($ex);}
            }
        }
    }

    public function validateValues()
    {
        if(!$this->hasErrors())
        {
            $values = $this->getValues();
            foreach($values as $index => $value)
            {
                try {$this->validateValueUsingIndex($index);}
                catch(MXT_FieldError $ex)
                    {$this->addError($ex);}
            }
        }
    }

    public function validateValueUsingIndex($index)
    {
        return true;
    }

    protected function validateConfirm()
    {
        if(!$this->hasErrors() && $this->isConfirmable())
        {
            $confirmField = $this->getConfirmField();

            if($this->getValue() != $confirmField->getValue() && $confirmField->getValue() != '')
                throw new MXT_FieldError(self::ERROR_CONFIRM_MISMATCH, $this->getErrorStatement('abstract', 'confirm-failed'));
        }
    }

    public function notifyOfFormErrors()
    {
    }


    protected function getErrorStatement($fieldKey, $key)
    {
        $error = $this->getStatement('error.' . $key, true);
        if(is_null($error))
        {
            MXT_Language::pushGroup('com.mephex.form.field.', $fieldKey);
            $error = $this->getStatement('error.' . $key);
            MXT_Language::popGroup();
        }

        return $error;
    }
}


?>
