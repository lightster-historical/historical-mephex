<?php


require_once PATH_LIB . 'com/mephex/data-object/filter/AbstractFilter.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/DefaultFilter.php';
require_once PATH_LIB . 'com/mephex/data-object/list/outputter/AbstractListOutputter.php';
require_once PATH_LIB . 'com/mephex/data-object/list/outputter/ListTableOutputter.php';
require_once PATH_LIB . 'com/mephex/util/range/InputPageRange.php';


abstract class MXT_DO_AbstractList
{
    protected $pageRange;

    protected $fields;
    protected $objects;

    protected $context;

    protected $filter;


    public function __construct()
    {
        $this->pageRange = null;
        $this->fields = null;
        $this->objects = null;

        $this->context = null;

        $this->filter = null;
    }


/*
    public function getPageRange()
    {
        if(is_null($this->pageRange))
        {
            $class = $this->getDataClass();

            $itemCount = $class->getRowCountUsingFilter($this->getFilter());
            $itemsPerPage = $this->getItemsPerPage();

            $this->pageRange = new MXT_InputPageRange($itemCount, $itemsPerPage, 'page');
        }

        return $this->pageRange;
    }
*/

    public function getItemsPerPage()
    {
        return 50;
    }


    protected function initFields()
    {
        $listFields = array();

        $dataClass = $this->getDataClass();
        $keynames = $this->getIncludedFields();

        foreach($keynames as $keyname)
        {
            if($this->isFieldIncluded($keyname))
            {
                $field = $dataClass->getField($keyname);
                $listFields[$keyname] = $field;
            }
        }

        return $listFields;
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


    protected function initDefaultFilter()
    {
        $this->filter = new MXT_DO_DefaultFilter($this);
    }

    public function getFilter()
    {
        if(is_null($this->filter))
        {
            $this->initDefaultFilter();
            //$this->filter->setPageRange($this->getPageRange());
        }

        return $this->filter;
    }

    public function setFilter(MXT_DO_AbstractFilter $filter)
    {
        $this->filter = $filter;
    }


    protected function initObjects()
    {
        $this->objects = array();

        $this->addObjects($this->loadObjects());
    }

    protected function loadObjects()
    {
        $dataClass = $this->getDataClass();
        return $dataClass->getObjectsUsingFilter($this->getFilter());
    }

    protected function addObjects($objs)
    {
        $pageRange = $this->getFilter()->getPageRange();
        $i = $pageRange->getMinOffset();

        foreach($objs as $obj)
        {
            $this->objects[$i] = $obj;
            $i++;
        }
    }


    public function getObjectUsingOffset($i)
    {
        if(is_null($this->objects))
            $this->initObjects();

        if(array_key_exists($i, $this->objects))
            return $this->objects[$i];

        return null;
    }


    public abstract function getDataClass();


    public function getFields()
    {
        if(is_null($this->fields))
            $this->fields = $this->initFields();

        return $this->fields;
    }

    public function isFieldIncluded($keyname)
    {
        $excluded = $this->getExcludedFields();
        return !in_array($keyname, $excluded);
    }

    public function getExcludedFields()
    {
        $dataClass = $this->getDataClass();
        $fields = $dataClass->getFields();
        $idField = $dataClass->getIdField();

        return array($idField->getKeyname());
    }


    public function getDisplayValueUsingFieldAndValue(MXT_AbstractDataField $field, $value)
    {
        return $field->getDisplayValue($this->getContext(), $value);
    }

    public function getDisplayValueUsingFieldAndObject(MXT_AbstractDataField $field, MXT_DataObject $obj)
    {
        $value = $obj->getValue($field->getKeyname());
        return $this->getDisplayValueUsingFieldAndValue($field, $value);
    }


    public function outputUsingOutputter(MXT_DO_AbstractListOutputter $outputter)
    {
        $outputter->outputList($this);
    }

    public function outputItemsUsingOutputter(MXT_DO_AbstractListOutputter $outputter)
    {
        $filter = $this->getFilter();
        $min = $filter->getLimitOffset();
        $max = $min + $filter->getLimitCount();
        #echo $filter->getLimitOffset() . ' ' . $filter->getLimitCount() . '<br />';

        for($i = $min; $i < $max; $i++)
        {
            $outputter->outputListObject($this, $i);
        }
    }


    public function getContext()
    {
        if(is_null($this->context))
            return MXT_DefaultContext::getDefaultContext();
        else
            return $this->context;
    }
}



?>
