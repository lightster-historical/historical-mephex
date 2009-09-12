<?php


require_once PATH_LIB . 'com/mephex/form/Form.php';


class MXT_FormError
{
    protected $index;
    protected $form;
    protected $defaultMessage;


    public function __construct(MXT_Form $form, $index, $defaultMessage)
    {
        $this->index = $index;
        $this->form = $form;
        $this->defaultMessage = $defaultMessage;
    }


    public function getIndex()
    {
        return $this->index;
    }

    public function getField()
    {
        return $this->form;
    }

    public function getDefaultMessage()
    {
        return $this->defaultMessage;
    }
}


?>
