<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/InputField.php';


class MXT_EmailField extends MXT_InputField
{
    const ERROR_EMAIL_INVALID = 1;


    public function __construct($keyname, MXT_FieldConstraint $constraint)
    {
        parent::__construct($keyname, $constraint);
    }

    public function validateValueUsingIndex($index)
    {
        if(!$this->isConfirmation())
        {
            if(!$this->hasErrors())
            {
                $regex = '/^[a-zA-Z0-9_-]+([\.a-zA-Z0-9_-]+)?@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
                if(!preg_match($regex, $this->getValue($index)))
                    throw new MXT_FieldError(self::ERROR_EMAIL_INVALID, $this->getErrorStatement('email', 'invalid'));
                    //throw new MXT_FieldError($this, self::ERROR_EMAIL_INVALID, 'This does not appear to be a valid e-mail address.');
            }
        }

        return !$this->hasErrors();
    }
}


?>
