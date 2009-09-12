<?php


require_once PATH_LIB . 'com/mephex/core/AbstractPageRange.php';
require_once PATH_LIB . 'com/mephex/core/PageRange.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/AbstractFilter.php';


class MXT_DO_DefaultFilter implements MXT_DO_AbstractFilter
{
    protected $list;
    protected $dataClass;
    protected $pageRange;

    protected $filterFields;
    
    protected $items;


    protected function __construct(MXT_DO_AbstractList $list)
    {
        $this->list = $list;
        $this->dataClass = $list->getDataClass();
        $this->pageRange = null;
        
        $this->itemCount = null;

        $this->filterFields = array();
    }


    public function getDataClass()
    {
        return $this->dataClass;
    }
    
    public function getList()
    {
        return $this->list;
    }
    
    
    

    public function getSortFields()
    {
        return array();
    }

    public function getFilterFields()
    {
        return $this->filterFields;
    }

    public function getFilterValues()
    {
        return array();
    }

    public function getLimitOffset()
    {
        return $this->getPageRange()->getMinOffset();
    }

    public function getLimitCount()
    {
        return $this->getPageRange()->getMaxOffset() - $this->getPageRange()->getMinOffset() + 1;
    }


    public function addFilterField($keyname, $defaultValue)
    {
        $this->filterFields[$keyname] = $defaultValue;
    }
    
    
    public function getItemCount()
    {
        if(is_null($this->itemCount))
            $this->itemCount = $this->getDataClass()->getRowCountUsingFilter($this);
            
        return $this->itemCount;
    }


    public function setPageRange(MXT_AbstractPageRange $pageRange = null)
    {
        $this->pageRange = $pageRange;
    }
    
    protected function initDefaultPageRange()
    {
        $this->pageRange = new MXT_PageRange(
            $this->getItemCount(), $this->getList()->getItemsPerPage(), 1);
    }
    
    public function getPageRange()
    {
        if(is_null($this->pageRange))
            $this->initDefaultPageRange();
        
        return $this->pageRange;
    }
}



?>
