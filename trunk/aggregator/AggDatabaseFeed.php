<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/aggregator/AggFeed.php';
require_once PATH_LIB . 'com/mephex/aggregator/AggItem.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';


class AggDatabaseFeed extends AggFeed
{
    protected $feedId;
    protected $keyName;
    protected $lastUpdated;


    protected function __construct($feedId)
    {
        parent::__construct();

        $this->feedId = $feedId;
        $this->keyName = null;
        $this->lastUpdated = null;
    }


    public function getFeedId()
    {
        return $feedId;
    }

    public function getKeyName()
    {
        return $this->keyName;
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }


    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    protected function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }


    public function saveProperties()
    {
        $db = Database::getConnection('com.mephex.aggregator');

        $query = new Query('UPDATE ' . $db->getTable('Feed') . ' SET '
            . '`lastUpdated`=\'' . Date::now('q Q') . '\', '
            . '`title`=\'' . addslashes($this->getTitle()) . '\', '
            . '`link`=\'' . addslashes($this->getLink()) . '\', '
            . '`description`=\'' . addslashes($this->getDescription()) . '\', '
            . '`language`=\'' . addslashes($this->getLanguage()) . '\', '
            . '`copyright`=\'' . addslashes($this->getCopyright()) . '\', '
            . '`pubDate`=\'' . $this->getPublishDate()->format('q Q', 0) . '\', '
            . '`lastBuildDate`=\'' . $this->getLastBuildDate()->format('q Q', 0) . '\', '
            . '`ttl`=\'' .  addslashes($this->getTimeToLive()) . '\', '
            . '`skipHours`=\'' .  addslashes($this->getSkipHours()) . '\', '
            . '`skipDays`=\'' .  addslashes($this->getSkipDays()) . '\' '
            . 'WHERE feedId=' . intval($this->feedId));
        $db->execQuery($query);
    }

    public function saveItems()
    {
        $db = Database::getConnection('com.mephex.aggregator');

        $items = $this->getItems();
        foreach($items as $item)
        {
            $query = new Query('INSERT IGNORE INTO ' . $db->getTable('Item')
                . '(`feedId`, `title`, `link`, `description`, `author`, '
                . '`guid`, `guidPermaLink`, `pubDate`) VALUES '
                . '(\'' . $this->feedId . '\', \''
                . addslashes($item->getTitle()) . '\', \''
                . addslashes($item->getLink()) . '\', \''
                . addslashes($item->getDescription()) . '\', \''
                . addslashes($item->getAuthor()) . '\', \''
                . addslashes($item->getGUId()) . '\', \''
                . addslashes($item->isPermaLink()) . '\', '
                . (is_null($item->getPublishDate()) ? 'NULL' : '\''
                . addslashes($item->getPublishDate()->format('q Q', 0)) . '\'') . ')');
            $result = $db->execQuery($query);
        }
    }


    public static function loadById($id)
    {
        $db = Database::getConnection('com.mephex.aggregator');

        $query = new Query('SELECT * FROM '
            . $db->getTable('Feed') . ' WHERE feedId=' . intval($id));
        return self::loadUsingQuery($query);
    }

    public static function loadByKeyName($keyName)
    {
        $db = Database::getConnection('com.mephex.aggregator');

        $query = new Query('SELECT * FROM '
            . $db->getTable('Feed') . ' WHERE keyName=\''
            . addslashes($keyName) . '\'');
        return self::loadUsingQuery($query);
    }

    protected static function loadUsingQuery(Query $query)
    {
        $db = Database::getConnection('com.mephex.aggregator');

        $result = $db->execQuery($query);
        if($row = $db->getAssoc($result))
        {
            $feed = new AggDatabaseFeed($row['feedId']);
            $feed->setURL($row['url']);
            $feed->setKeyName($row['keyName']);
            $feed->setLastUpdated(new Date($row['lastUpdated']));
            $feed->setTitle($row['title']);
            $feed->setLink($row['link']);
            $feed->setDescription($row['description']);
            $feed->setLanguage($row['language']);
            $feed->setCopyright($row['copyright']);
            $feed->setPublishDate(new Date($row['pubDate']));
            $feed->setLastBuildDate(new Date($row['lastBuildDate']));
            $feed->setTimeToLive($row['ttl']);
            $feed->setSkipHours($row['skipHours']);
            $feed->setSkipDays($row['skipDays']);

            return $feed;
        }

        return null;
    }

    public function loadItems($count = -1)
    {
    }


    public static function create($keyName, $url)
    {
        return null;
    }

}



?>
