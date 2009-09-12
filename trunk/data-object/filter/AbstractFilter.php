<?php


interface MXT_DO_AbstractFilter
{
    public function getDataClass();

    public function getSortFields();

    public function getFilterFields();
    public function getFilterValues();

    public function getLimitOffset();
    public function getLimitCount();
}



?>
