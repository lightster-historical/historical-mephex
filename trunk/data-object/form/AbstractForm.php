<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/Form.php';
require_once PATH_LIB . 'com/mephex/form/field/SubmitField.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


// added: 2009/03/15 20:09
abstract class MXT_DO_AbstractForm extends MXT_Form
{
    protected $input;


    public function __construct($action)
    {
        parent::__construct($action, 'post');

        $this->input = new Input($_REQUEST);
    }


    public function getInput()
    {
        return $this->input;
    }


    protected function initFields()
    {
        parent::initFields();

        $dataClass = $this->getDataClass();
        $keynames = $this->getIncludedFields();
        $idField = $dataClass->getIdField();

        $this->addSimpleFieldset('basic');
        foreach($keynames as $keyname)
        {
            if($this->isFieldIncluded($keyname))
            {
                $field = $dataClass->getField($keyname);
                if($field != $idField)
                    $this->addDataField($field->getKeyname());
            }
        }

        $this->initSubmitFieldset();

        $this->setInputs($this->getInput());
    }

    public final function getDefaultIncludedFields()
    {
        $dataClass = $this->getDataClass();
        $fields = $dataClass->getFields();

        $keynames = array();
        foreach($fields as $field)
            $keynames[] = $field->getKeyname();

        return $keynames;
    }

    public function getIncludedFields()
    {
        return $this->getDefaultIncludedFields();
    }


    public function initSubmitFieldset()
    {
        $dataClass = $this->getDataClass();
        $idField = $dataClass->getIdField();

        $this->addSimpleFieldset('submit');
        $this->addDataContextSubmit('submit_create', 'submit_save');
        $this->addField(new MXT_SubmitField('submit_cancel'));
        $this->addDataField($idField->getKeyname());
    }


    public abstract function getDataClass();
    public abstract function getDataObject();

    public function isCreated()
    {
        $obj = $this->getDataObject();

        return (!is_null($obj) && $obj->isCreated());
    }


    public function isFieldIncluded($keyname)
    {
        $excluded = $this->getExcludedFields();
        return !in_array($keyname, $excluded);
    }

    public function getExcludedFields()
    {
        return array();
    }


    protected function addDataField($keyname)
    {
        $class = $this->getDataClass();
        $obj = $this->getDataObject();

        $dataField = $class->getField($keyname);
        $formField = $dataField->getFormField();

        if(!is_null($formField))
        {
            $value = $dataField->encodeValueForForm($obj->getValue($keyname));
            $formField->setValue($value);
            $this->addField($formField);
        }
    }

    protected function addDataContextSubmit($create, $save)
    {
        if($this->isCreated())
            $this->addField(new MXT_SubmitField($save));
        else
            $this->addField(new MXT_SubmitField($create));
    }


    public abstract function getSaveListener();

    public function submit()
    {
        if(count($_POST) > 0)
        {
            $this->setValuesUsingInput($this->getInput());

            if(!is_null($this->getValueShortcut('submit_create'))
                || !is_null($this->getValueShortcut('submit_save')))
            {
                $obj = $this->getDataObject();
                if($this->validate())
                {
                    $dataClass = $this->getDataClass();
                    $formFields = $this->getFields();

                    foreach($formFields as $formField)
                    {
                        try
                        {
                            $key = $formField->getKeyname();
                            $dataField = $dataClass->getField($key);
                            $value = $dataField->decodeValueFromForm($formField->getValue());

                            $obj->setValue($key, $value);
                        }
                        catch(Exception $ex)
                        {
                        }
                    }

                    return $this->saveObject($obj);
                }
                else
                {
                    if($obj->isCreated())
                    {
                        $this->getSaveListener()->updateFailed(new MXT_DataObjectSaveEvent($obj));
                    }
                    else
                    {
                        $this->getSaveListener()->createFailed(new MXT_DataObjectSaveEvent($obj));
                    }
                }
            }
            else
            {
                $this->saveCanceled();
            }
        }

        return false;
    }

    protected function saveObject(MXT_DataObject $obj)
    {
        return $obj->saveUsingWriter($this->getWriter(), $this->getSaveListener());
    }

    protected function getWriter()
    {
        return $this->getDataClass()->getWriter();
    }

    protected function saveCanceled()
    {
    }
}



?>
