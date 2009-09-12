<?php


require_once PATH_LIB . 'com/mephex/core/AbstractPageRange.php';


class MXT_PageRange extends MXT_AbstractPageRange
{
    protected $itemCount;
    protected $itemsPerPage;
    protected $currPage;


    public function __construct($itemCount, $itemsPerPage, $currPage)
    {
        $this->itemCount = $itemCount;
        $this->itemsPerPage = max($itemsPerPage, 0);

        $this->currPage = max(1, min($currPage, $this->getPageCount()));
    }


    public function getItemCount()
    {
        return $this->itemCount;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage()
    {
        return $this->currPage;
    }
}


?>
