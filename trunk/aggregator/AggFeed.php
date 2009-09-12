<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/aggregator/AggItem.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';


class AggFeed
{
    protected $url;

    protected $title;
    protected $link;
    protected $description;

    protected $language;
    protected $copyright;

    protected $pubDate;
    protected $lastBuildDate;

    protected $ttl;
    protected $skipHours;
    protected $skipDays;

    protected $items;


    protected function __construct()
    {
        $this->url = null;

        $this->title = null;
        $this->link = null;
        $this->description = null;

        $this->language = null;
        $this->copyright = null;

        $this->pubDate = null;
        $this->lastBuildDate = null;

        $this->ttl = null;
        $this->skipHours = 0;
        $this->skipDays = 0;

        $this->items = array();
    }


    public function getURL()
    {
        return $this->url;
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


    public function getLanguage()
    {
        return $this->language;
    }

    public function getCopyright()
    {
        return $this->copyright;
    }


    public function getPublishDate()
    {
        return $this->pubDate;
    }

    public function getLastBuildDate()
    {
        return $this->lastBuildDate;
    }


    public function getTimeToLive()
    {
        return $this->ttl;
    }

    public function getSkipHours()
    {
        return $this->skipHours;
    }

    public function isSkipHour($hour)
    {
        if(0 <= $hour && $hour <= 23)
        {
            $power = pow(2, $hour);

            return ($this->skipHours & $power) >= 1 ? true : false;
        }

        return null;
    }

    public function getSkipDays()
    {
        return $this->skipDays;
    }

    public function isSkipDay($day)
    {
        if(0 <= $day && $day <= 6)
        {
            $power = pow(2, $day);

            return ($this->skipDays & $power) >= 1 ? true : false;
        }

        return null;
    }


    public function getItems()
    {
        return $this->items;
    }


    public function setURL($url)
    {
        $this->url = $url;
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


    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }


    public function setPublishDate(Date $pubDate)
    {
        $this->pubDate = $pubDate;
    }

    public function setLastBuildDate(Date $lastBuildDate)
    {
        $this->lastBuildDate = $lastBuildDate;
    }


    public function setTimeToLive($ttl)
    {
        $this->ttl = $ttl;
    }

    public function setSkipHours($skipHours)
    {
        $this->skipHours = $skipHours;
    }

    public function setSkipHour($hour, $skip = true)
    {
        if(0 <= $hour && $hour <= 23)
        {
            $power = pow(2, $hour);

            if($skip)
                $this->skipHours = $this->skipHours | $power;
            else
                $this->skipHours = $this->skipHours & ~$power;
        }
    }

    public function setSkipDays($skipDays)
    {
        $this->skipDays = $skipDays;
    }

    public function setSkipDay($day, $skip = true)
    {
        if(!(0 <= $day && $day <= 6))
        {
            $day = strtolower($day);
            $days = array('sunday' => 0, 'sun' => 0, 'monday'=> 1, 'mon' => 1,
                'tuesday' => 2, 'tues' => 2, 'wednesday' => 3, 'wed' => 3,
                'thursday' => 4, 'thurs' => 4, 'friday' => 5, 'fri' => 5,
                'saturday' => 6, 'sat' => 6);

            if(array_key_exists($day, $days))
                $day = $days[$day];
        }

        if(0 <= $day && $day <= 6)
        {
            $power = pow(2, $day);

            if($skip)
                $this->skipDays = $this->skipDays | $power;
            else
                $this->skipDays = $this->skipDays & ~$power;
        }
    }


    public function addItem(AggItem $item)
    {
        $this->items[] = $item;
    }
}


?>
