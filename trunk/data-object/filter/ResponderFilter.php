<?php


require_once PATH_LIB . 'com/mephex/data-object/filter/DefaultFilter.php';


class MXT_DO_ResponderFilter extends MXT_DO_DefaultFilter
{
    protected $responder;


    public function __construct(MXT_DO_AbstractManageResponder $responder)
    {
        parent::__construct($responder->getList());

        $this->responder = $responder;
    }


    public function getResponder()
    {
        return $this->responder;
    }


    public function getFilterFields()
    {
        return $this->getResponder()->getFilterFields();
    }

    public function getSortFields()
    {
        return $this->getResponder()->getSortFields();
    }

    public function getFilterValues()
    {
        $responder = $this->getResponder();
        $class = $this->getDataClass();

        $input = new Input($_REQUEST);

        $values = array();
        $fields = $this->getFilterFields();
        foreach($fields as $keyname => $defaultValue)
        {
            if($class->isField($keyname))
            {
                $field = $class->getField($keyname);
                if($input->set($keyname))
                {
                    $values[$keyname] = $field->decodeValueFromForm($input->get($keyname));
                }
                else
                {
                    $values[$keyname] = $defaultValue;
                }
            }
        }

        return $values;
    }
    
    
    protected function initDefaultPageRange()
    {
        $this->pageRange = new MXT_InputPageRange($this->getItemCount(), $this->getList()->getItemsPerPage(), 'page');
        #print_r($this->pageRange);die();
    }




    /*
    public function addSortField($keyname, $direction)
    {
        $this->sortFields[$keyname] = $direction;
    }

    public function addFilterField($keyname, $values)
    {
        $this->filterFields[$keyname] = $values;
    }

    public function setLimit($offset, $count)
    {
        $this->limitOffset = $offset;
        $this->limitCount = $count;
    }
    */
}



?>
