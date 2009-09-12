<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/aggregator/AggFeed.php';
require_once PATH_LIB . 'com/mephex/aggregator/AggItem.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';



define('AGGREGATOR_NONE', 0);
define('AGGREGATOR_RSS', 1);
define('AGGREGATOR_ATOM', 1);



class AggParser
{
    protected $type;

    protected $feed;
    protected $version;

    protected $openTags;
    protected $currentTag;
    protected $lastDataTag;

    protected $currentItem;

    protected $skipDaysOpen;
    protected $skipHoursOpen;


    protected function __construct()
    {
        $this->type = AGGREGATOR_NONE;

        $this->version = 0.0;

        $this->openTags = array();
        $this->currentTag = '';

        $this->currentItem = null;

        $this->skipDaysOpen = false;
        $this->skipHoursOpen = false;
    }


    public static function parseFile($addr, AggParser $parser = null, $cacheAddr = null)
    {
        if(is_null($parser))
            $parser = new AggParser();

        $xml = file_get_contents($addr);
        $xml = preg_replace('!<copyright>(.*?)</copyright>!', '', $xml);

        if($xml === false)
            trigger_error($addr . ' could not be opened for reading.', E_USER_ERROR);

        if(!is_null($cacheAddr))
        {
            if($fwh = @fopen($cacheAddr, 'wb'))
            {
                fwrite($fwh, $xml, strlen($xml));
                fclose($fwh);
            }
            else
            {
                trigger_error($cacheAddr . ' could not be opened for writing.', E_USER_WARNING);
            }
        }

        return self::parseXML($xml, $parser);
    }


    public static function parseXML($xml, AggParser $parser = null)
    {
        if(is_null($parser))
            $parser = new AggParser();

        $xmlParser = xml_parser_create();

        xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, false);

        xml_set_object($xmlParser, $parser);
        xml_set_element_handler($xmlParser, 'openTag', 'closeTag');
        xml_set_character_data_handler($xmlParser, 'readTag');

        xml_parse($xmlParser, $xml);

        xml_parser_free($xmlParser);

        $parser->finalizeFeed();

        return $parser->feed;
    }

    public function openTag($xmlParser, $name, $attributes)
    {
        $name = strtolower($name);

        /*if(!is_null($this->feed))
            echo $this->feed->getTitle();
        echo ":$name\n";*/

        if($name == 'rss' || $name == 'feed')
        {
            $this->createFeed();

            if(array_key_exists('version', $attributes))
                $this->version  = $attributes['version'];

            if($name == 'rss')
                $this->type = AGGREGATOR_RSS;
            else if($name == 'feed')
                $this->type = AGGREGATOR_ATOM;
        }
        else if($name == 'item' || $name == 'entry')
        {
            $this->createItem();
        }
        else if($name == 'skipdays')
            $this->skipDaysOpen = true;
        else if($name == 'skiphours')
            $this->skipHoursOpen = true;

        //if($this->lastDataTag != $this->currentTag)
            //$this->closeTag($xmlParser, $name);

        $this->openTags[] = $name;
        $this->currentTag = $name;
    }

    function readTag($xmlParser, $data)
    {
        $this->lastDataTag = $this->currentTag;
        //echo $this->currentTag;
        if(!is_null($this->currentItem))
        {
            switch($this->currentTag)
            {
                case 'title':
                    $this->currentItem->setTitle($data);
                    break;
                case 'link':
                    $this->currentItem->setLink($data);
                    break;
                case 'description':
                case 'summary':
                    $this->currentItem->appendDescription($data);
                    break;
                case 'author':
                    $this->currentItem->setAuthor($data);
                    break;
                case 'guid':
                    $this->currentItem->setGUId($data);
                    break;
                case 'id':
                    $this->currentItem->setLink($data);
                    $this->currentItem->setGUId($data);
                    break;
                case 'pubdate':
                case 'published':
                case 'updated':
                    if($this->openTags[0] == 'feed')
                        $data = str_replace('T', ' ', $data);
                    $this->currentItem->setPublishDate(new Date($data));
                    break;
            }
        }
        else if(!is_null($this->feed))
        {
            switch($this->currentTag)
            {
                case 'title':
                    $this->feed->setTitle($data);
                    break;
                case 'link':
                    $this->feed->setLink($data);
                    break;
                case 'id':
                    $this->feed->setLink($data);
                    break;
                case 'description':
                case 'subtitle':
                    $this->feed->appendDescription($data);
                    break;
                case 'language':
                    $this->feed->setLanguage($data);
                    break;
                case 'copyright':
                case 'rights':
                    $this->feed->setCopyright($data);
                    break;
                case 'pubdate':
                case 'updated':
                    if($this->openTags[0] == 'feed')
                        $data = str_replace('T', ' ', $data);
                    $this->feed->setPublishDate(new Date($data));
                    break;
                case 'lastbuilddate':
                    if($this->openTags[0] == 'feed')
                        $data = str_replace('T', ' ', $data);
                    $this->feed->setLastBuildDate(new Date($data));
                    break;
                case 'ttl':
                    $this->feed->setTimeToLive($data);
                    break;
                case 'day':
                    if($this->skipDaysOpen)
                        $this->feed->setSkipDay($data, true);
                    break;
                case 'hour':
                    if($this->skipHoursOpen)
                        $this->feed->setSkipHour($data, true);
                    break;
                default:
                    //echo $this->currentTag . ' not recognized' . ': ';
                    //print_r($this->openTags);
                    //echo '<br />';
            }
        }
    }

    function closeTag($xmlParser, $name)
    {
        /*echo '/';
        if(!is_null($this->feed))
            echo $this->feed->getTitle();
        echo ":$name\n";*/

        array_pop($this->openTags);
        $this->currentTag = end($this->openTags);

        if($name == 'item' || $name == 'entry')
        {
            $this->addItem($this->currentItem);
            $this->currentItem = null;
        }
        else if($name == 'skipdays')
            $this->skipDaysOpen = false;
        else if($name == 'skiphours')
            $this->skipHoursOpen = false;
    }


    function getVersion()
    {
        return $this->version;
    }


    protected function createFeed()
    {
        $this->feed = new AggFeed();
    }

    protected function finalizeFeed()
    {
    }

    protected function createItem()
    {
        $this->currentItem = new AggItem();
    }

    protected function addItem($item)
    {
        $this->feed->addItem($item);
    }
}


?>
