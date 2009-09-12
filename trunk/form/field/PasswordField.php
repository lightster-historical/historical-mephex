<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';


class MXT_PasswordField extends MXT_InputField
{
    const ERROR_PASSWORD_SHORT = 1;
    const ERROR_CONFIRM_REQUIRED = 2;
    const ERROR_FORM_ERROR = 4;
    const ERROR_FORM_CONFIRM = 8;


    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }


    public function setInputs(Input $input)
    {
        return $input->set($this->getKeyname());
    }


    public function printFieldInputAsHTML($index = null)
    {
        ?>
         <input type="password" name="<?php echo $this->getKeynameWithIndex($index); ?>" value="" />
        <?php
    }

    public function validateField()
    {
        if(!$this->isConfirmation())
        {
            parent::validateField();
        }

        return !$this->hasErrors();
    }

    public function validateValueUsingIndex($index)
    {
        if(!$this->isConfirmation())
        {
            // check to see if the password is long enough
            if(!$this->hasErrors() && strlen($this->getValue($index)) < 6)
            {
                throw new MXT_FieldError(self::ERROR_PASSWORD_SHORT, $this->getErrorStatement('password', 'too-short'));
                //throw new MXT_FieldError($this, self::ERROR_PASSWORD_SHORT, 'The provided ' . $this->getTitle() . ' is too short.');
            }

            // check to see if the confirmation password is provided (if required)
            $confirmField = $this->getConfirmField();
            if(!$this->hasErrors() && (!is_null($confirmField) && $confirmField->getValue($index) == ''))
            {
                throw new MXT_FieldError(self::ERROR_CONFIRM_REQUIRED, $this->getErrorStatement('password', 'failed-confirmation'));
                //throw new MXT_FieldError($this, self::ERROR_CONFIRM_REQUIRED, 'Re-enter your password and password confirmation.');
            }
        }

        return !$this->hasErrors();
    }

    public function notifyOfFormErrors()
    {
        parent::notifyOfFormErrors();

        $confirmField = $this->getConfirmField();
        if(!$this->hasErrors() && !$this->isConfirmation())
        {
            if(!is_null($confirmField))
                throw new MXT_FieldError(self::ERROR_FORM_CONFIRM, $this->getErrorStatement('password', 'form-error-with-confirm'));
                //throw new MXT_FieldError($this, self::ERROR_FORM_CONFIRM, 'Re-enter your password and password confirmation.');
            else
                throw new MXT_FieldError(self::ERROR_FORM, $this->getErrorStatement('password', 'form-error'));
                //throw new MXT_FieldError($this, self::ERROR_FORM, 'Re-enter your password.');
        }
    }
}


?>
