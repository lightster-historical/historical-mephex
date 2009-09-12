<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/aggregator/AggDatabaseFeed.php';
require_once PATH_LIB . 'com/mephex/aggregator/AggItem.php';
require_once PATH_LIB . 'com/mephex/aggregator/AggParser.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';



class AggDatabaseParser extends AggParser
{
    protected function __construct()
    {
        parent::__construct();
    }


    public static function parseFeedById($id, $force = false)
    {
        $parser = self::getParserForFeedId($id);
        if(is_null($parser))
            die("Parser cannot be loaded for " . $id);

        $feed = $parser->feed;
        $lastUpdated = $feed->getLastUpdated();
        $earliestUpdate = new Date($lastUpdated);
        $earliestUpdate->changeMinute($feed->getTimeToLive());

        $now = new Date();

        if((
            $earliestUpdate->compareTo($now) <= 0
            && !$feed->isSkipDay($now->format('w'))
            && !$feed->isSkipHour($now->format('G'))
           )
           || $force)
        {
            AggParser::parseFile($feed->getURL(), $parser);//, $parser->feed->getKeyName() . '.xml');
        }
    }


    public static function getParserForFeedId($id)
    {
        $parser = new AggDatabaseParser();
        $parser->feed = AggDatabaseFeed::loadById($id);
        if(is_null($parser->feed))
            return null;

        return $parser;
    }

    public static function createParserForURL($keyName, $url)
    {
        $parser = new AggDatabaseParser();
        $parser->feed = AggDatabaseFeed::create($keyName, $url);
        if(is_null($parser->feed))
            return null;

        return $parser;
    }


    protected function createFeed()
    {
        // if a database parser exists, the feed should already be created
        // and referenced by $this->feed
    }

    protected function finalizeFeed()
    {
        $this->feed->saveProperties();
        $this->feed->saveItems();
    }

    /*
    protected function createItem()
    {
        $this->currentItem = new AggItem();
    }

    protected function addItem($item)
    {
        $this->feed->addItem($item);
    }
    */
}



?>
