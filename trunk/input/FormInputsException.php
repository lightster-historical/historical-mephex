<?php


class FormInputsException extends Exception
{
    protected $errors;
    protected $values;


    public function __construct($errors, $values)
    {
        parent::__construct('There was an error in one or more of the form values.');

        $this->errors = $errors;
        $this->values = $values;
    }


    public function getErrors()
    {
        return $this->errors;
    }

    public function getValues()
    {
        return $this->values;
    }

}


?>
