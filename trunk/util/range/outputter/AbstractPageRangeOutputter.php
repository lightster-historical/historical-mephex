<?php


require_once PATH_LIB . 'com/mephex/core/AbstractPageRange.php';


interface MXT_AbstractPageRangeOutputter
{
    public function outputPageRange(MXT_AbstractPageRange $pageRange);
}



?>
