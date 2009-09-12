<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/core/Date.php';


class AggItem
{
    protected $title;
    protected $link;
    protected $description;

    protected $author;

    protected $guid;
    protected $guidPermaLink;

    protected $pubDate;


    public function __construct()
    {
        $this->title = null;
        $this->link = null;
        $this->description = '';

        $this->author = null;

        $this->guid = null;
        $this->guidPermaLink = true;

        $this->pubDate = null;
    }



    public function getTitle()
    {
        return $this->title;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAuthor()
    {
        return $this->author;
    }


    public function getGUId()
    {
        return $this->guid;
    }

    public function isPermaLink()
    {
        return $this->guidPermaLink;
    }


    public function getPublishDate()
    {
        return $this->pubDate;
    }



    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function appendDescription($description)
    {
        $this->description .= $description;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }


    public function setGUId($guid)
    {
        $this->guid = $guid;
    }

    public function setPermaLink($isPermaLink)
    {
        $this->guidPermaLink = $isPermaLink;
    }


    public function setPublishDate(Date $pubDate)
    {
        $this->pubDate = $pubDate;
    }
}


?>
