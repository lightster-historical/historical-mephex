<?php


require_once PATH_LIB . 'com/mephex/nav/pagenum/AbstractPageRange.php';


interface MXT_AbstractPageNumFilter
{
    const SPECIAL_FIRST = 1;
    const SPECIAL_PREV = 2;
    const SPECIAL_NEXT = 4;
    const SPECIAL_LAST = 8;
    
    
    public function isPageNumberIncluded(MXT_AbstractPageRange $range, $pageNum);
    public function isSpecialIncluded(MXT_AbstractPageRange $range, $special);
}



?>