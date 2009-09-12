<?php


interface MXT_CacheableContent
{
    public function getContent();
    public function getContentLastUpdated();

    public function getDirectory();
    public function getFileName();
}


?>
