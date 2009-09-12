<?php


require_once PATH_LIB . 'com/mephex/util/range/outputter/AbstractPageRangeOutputter.php';
require_once PATH_LIB . 'com/mephex/util/range/outputter/DefaultPageRangeOutputter.php';


abstract class MXT_AbstractPageRange
{
    public abstract function getItemCount();
    public abstract function getItemsPerPage();
    public abstract function getCurrentPage();


    public function getPageCount()
    {
        $itemCount = $this->getItemCount();
        $itemsPerPage = $this->getItemsPerPage();

        if($itemsPerPage == 0)
            $pages = 1;
        else
            $pages = ceil($itemCount / $itemsPerPage);

        return intval($pages);
    }


    public function getMinOffset()
    {
        $pageOffset = $this->getCurrentPage() - 1;
        $itemsPerPage = $this->getItemsPerPage();

        return intval($pageOffset * $itemsPerPage);
    }

    public function getMaxOffset()
    {
        $itemCount = $this->getItemCount();
        $pageOffset = $this->getCurrentPage();
        $itemsPerPage = $this->getItemsPerPage();

        $offset = ($pageOffset * $itemsPerPage) - 1;
        //if($itemCount == 0)
        //    $offset = -1;
        //else
        if($itemsPerPage == 0 || $offset >= $itemCount)
            $offset = $itemCount - 1;

        return intval($offset);
    }


    public function outputUsingOutputter(MXT_AbstractPageRangeOutputter $outputter)
    {
        $outputter->outputPageRange($this);
    }

    public function outputDefault()
    {
        $outputter = new MXT_DefaultPageRangeOutputter();
        $outputter->outputPageRange($this);
    }
}


?>
