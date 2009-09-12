<?php


require_once PATH_LIB . 'com/mephex/util/range/outputter/AbstractPageRangeOutputter.php';


class MXT_DefaultPageRangeOutputter implements MXT_AbstractPageRangeOutputter
{
    public function outputPageRange(MXT_AbstractPageRange $pageRange)
    {
        $currPage = $pageRange->getCurrentPage();
        $pageCount = $pageRange->getPageCount();

        ?>
         <div class="page-range-default">
          <ol>
        <?php
        if($currPage > 1)
        {
            if($currPage > 2)
                $this->outputFirstPage();

            $this->outputPreviousPage($currPage);
        }

        for($i = max($currPage - 2, 1); $i <= min($currPage + 2, $pageCount); $i++)
        {
            if($currPage == $i)
                $this->outputCurrentPage($currPage);
            else
                $this->outputPage($i, $i);
        }

        if($currPage < $pageCount)
        {
            $this->outputNextPage($currPage);

            if($currPage < $pageCount - 1)
                $this->outputLastPage($pageCount);
        }
        ?>
          </ol>
          <br class="clear" />
         </div>
        <?php
    }


    public function outputPageLink($page, $title)
    {
        echo "<a href=\"?page=$page\">$title</a>";
    }


    public function outputPage($page, $title)
    {
        echo "<li>";
        $this->outputPageLink($page, $title);
        echo "</li>";
    }

    public function outputSpecialPage($class, $page, $title)
    {
        echo "<li class=\"$class\">";
        $this->outputPageLink($page, $title);
        echo "</li>";
    }


    public function outputFirstPage()
    {
        $this->outputSpecialPage('first-page', 1, '&laquo;');
    }

    public function outputPreviousPage($currPage)
    {
        $this->outputSpecialPage('prev-page', $currPage - 1, '&lt;');
    }

    public function outputCurrentPage($currPage)
    {
        $this->outputSpecialPage('curr-page', $currPage, $currPage);
    }

    public function outputNextPage($currPage)
    {
        $this->outputSpecialPage('next-page', $currPage + 1, '&gt;');
    }

    public function outputLastPage($pageCount)
    {
        $this->outputSpecialPage('last-page', $pageCount, '&raquo;');
    }
}



?>
