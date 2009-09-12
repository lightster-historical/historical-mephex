<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/AbstractFilter.php';
require_once PATH_LIB . 'com/mephex/form/Form.php';
require_once PATH_LIB . 'com/mephex/form/field/SubmitField.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


// added: 2009/03/15 20:09
class MXT_DO_FilterForm extends MXT_Form
{
    protected $filter;

    protected $input;

    protected $fields;


    public function __construct(MXT_DO_DefaultFilter $filter)
    {
        parent::__construct($_SERVER['PHP_SELF'], 'get');

        $this->filter = $filter;

        $this->input = new Input($_REQUEST);
        $this->initFields();
    }


    public function getFilter()
    {
        return $this->filter;
    }

    public function getInput()
    {
        return $this->input;
    }


    protected function initFields()
    {
        $filter = $this->getFilter();
        $dataClass = $filter->getDataClass();
        $idField = $dataClass->getIdField();

        $this->addSimpleFieldset('basic');
        $filterFields = $filter->getFilterValues();
        foreach($filterFields as $keyname => $value)
        {
            if($dataClass->isField($keyname))
            {
                $dataField = $dataClass->getField($keyname);
                if($dataField != $idField)
                {
                    $formField = $dataField->getFormField($dataField);

                    if(!is_null($formField))
                    {
                        $value = $dataField->encodeValueForForm($value);
                        $this->addField($formField);
                        $formField->setValue($value);
                    }
                }
            }
        }

        $submitField = new MXT_SubmitField('submitFilter');
        $submitField->enableNameOutput(false);
        $this->addField($submitField);
    }
}



?>
