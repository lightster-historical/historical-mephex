<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/form/ResponderForm.php';
require_once PATH_LIB . 'com/mephex/form/field/HiddenField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/MinMaxConstraint.php';


class MXT_DO_ResponderForm extends MXT_DO_DefaultForm
{
    protected $responder;


    public function __construct(MXT_DO_AbstractManageResponder $responder
        , $action, $id)
    {
        $this->responder = $responder;

        // second argument requires a SaveListener,
        // which, in this case, the responder implements
        parent::__construct($responder->getDataObject(), $responder, $action, $id);
    }


    public function initFields()
    {
        parent::initFields();

        $input = $this->getInput();
        if($input->set('action'))
        {
            $action = $input->get('action');

            $actionField = new MXT_HiddenField('action', new MXT_MinMaxConstraint());
            $actionField->setValue($action);
            $this->addField($actionField);

            if($action == 'create')
            {
                $responder = $this->getResponder();
                $filter = $responder->getFilter();
                $filterValues = $filter->getFilterValues();
                foreach($filterValues as $keyname => $value)
                {
                    $formField = $this->getFieldUsingShortcut($keyname);
                    if($value instanceof MXT_DataObject)
                    {
                        $formField->setValue($value->getId());
                    }
                }
            }
        }
    }


    public function getResponder()
    {
        return $this->responder;
    }


    public function getIncludedFields()
    {
        return $this->getResponder()->getIncludedFormFields();
    }

    public function getExcludedFields()
    {
        return $this->getResponder()->getExcludedFormFields();
    }


    protected function saveObject(MXT_DataObject $obj)
    {
        $field = $this->getFieldUsingShortcut('action');
        if(!is_null($field) && $field->getValue() == 'create')
        {
            $obj = clone $obj;
            $obj->setId(0);
        }

        parent::saveObject($obj);
    }

    protected function saveCanceled()
    {
        HttpHeader::forwardTo($_SERVER['PHP_SELF']);
    }
}



?>
