<?php


require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/ResponderFilter.php';
require_once PATH_LIB . 'com/mephex/data-object/list/DefaultList.php';


class MXT_DO_ResponderList extends MXT_DO_DefaultList
{
    protected $responder;


    public function __construct(MXT_DO_AbstractManageResponder $responder
        , $itemsPerPage = 0)
    {
        $this->responder = $responder;

        $class = $responder->getDataClass();

        parent::__construct($class, $itemsPerPage);
    }


    public function getFilter()
    {
        if(is_null($this->filter))
        {
            $this->filter = new MXT_DO_ResponderFilter($this->getResponder());
            //$this->filter->setPageRange($this->getPageRange());
        }

        return $this->filter;
    }


    public function getResponder()
    {
        return $this->responder;
    }


    public function getDisplayValueUsingFieldAndValue(MXT_AbstractDataField $field, $value)
    {
        $responder = $this->getResponder();

        if($responder->hasCustomListDisplayValue($field, $value))
            return $responder->getListDisplayValueUsingFieldAndValue($field, $value);

        return $field->getDisplayValue($this->getContext(), $value);
    }


    public function getIncludedFields()
    {
        return $this->getResponder()->getIncludedListFields();
    }

    public function getExcludedFields()
    {
        return $this->getResponder()->getExcludedListFields();
    }
}



?>
