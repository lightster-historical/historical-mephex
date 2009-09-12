<?php


require_once PATH_LIB . 'com/mephex/nav/pagenum/filter/AbstractPageNumFilter.php';


class MXT_DefaultPageNumFilter implements MXT_AbstractPageNumFilter
{
    protected $leftCount;
    protected $rightCount;
    
    
    public function __construct($maxCount = 13)
    {
        if(($maxCount % 2) == 1)
            --$maxCount;
            
        $this->leftCount = $this->rightCount = intval($maxCount / 2);
    }
    
    
    public function getLeftCount()
    {
        return $this->leftCount;
    }
    
    public function getRightCount()
    {
        return $this->rightCount;
    }
    
    
    
    public function isPageNumberIncluded(MXT_AbstractPageRange $range, $pageNum)
    {
        // 10/30 (1...10...30)
        $curr = $range->getCurrentPage();
        $pageCount = $range->getPageCount();
        
        // 5/5
        $leftCount = $this->getLeftCount();
        $rightCount = $this->getRightCount();
        
        // 5/15 (1...5,6,7,8,9,10,11,12,13,14,15...30)
        $leftNum = max($curr - $leftCount, 1);
        $rightNum = min($curr + $rightCount, $pageCount);
        
        return ($leftNum <= $pageNum && $pageNum <= $rightNum);
    }
    
    public function isSpecialIncluded(MXT_AbstractPageRange $range, $special)
    {
        $curr = $range->getCurrentPage();
        $toGo = $range->getPageCount() - $curr;
        
        switch($special)
        {
            case self::SPECIAL_FIRST:
                return ($curr > 1);
            case self::SPECIAL_PREV:
                return ($curr > 2);
            case self::SPECIAL_NEXT:
                return ($toGo > 2);
            case self::SPECIAL_LAST:
                return ($toGo > 1);
        }
        
        return false;
    }
}



?>