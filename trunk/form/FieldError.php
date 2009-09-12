<?php


require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';


class MXT_FieldError extends Exception
{
    protected $index;
    protected $key;

    protected $next;


    public function __construct($index, $messageKey)
    {
        parent::__construct(MXT_Language::getStatement($messageKey), $index);

        $this->index = $index;
        $this->key = $messageKey;

        $this->next = null;
    }


    public function getIndex()
    {
        return $this->index;
    }


    public function addError(MXT_FieldError $error)
    {
        if(is_null($this->next))
            $this->next = $error;
        else
            $this->next->addError($error);
    }

    public function getNext()
    {
        return $this->next;
    }
}


?>
