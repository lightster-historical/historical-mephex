<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/aggregator/AggItem.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/core/DateRange.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';


class AggDatabaseItem extends AggItem
{
    public static function getItems($feeds = array(), $count = -1, $start = 0, DateRange $dateRange = null)
    {
        $db = Database::getConnection('com.mephex.aggregator');

        if(!is_array($feeds))
            $feeds = array($feeds);

        $ids = array();
        $keyNames = array();

        foreach($feeds as $feed)
        {
            if(is_numeric($feed))
                $ids[] = intval($feed);
            else if($feed instanceof AggDatabaseFeed)
                $ids[] = intval($feed->getFeedId());
            else
                $keyNames[] = '\'' . addslashes($feed) . '\'';
        }

        $where = array();
        $keyNameJoin = '';
        if(count($ids) > 0)
        {
            $where[] = ' i.`feedId` IN (' . implode(',', $ids) . ')';
        }
        if(count($keyNames) > 0)
        {
            $where[] = ' f.`keyName` IN (' . implode(',', $keyNames) . ')';
            $keyNameJoin = 'LEFT JOIN ' . $db->getTable('Feed') . ' AS f'
                . ' ON i.feedId=f.feedId ';
        }
        if(!is_null($dateRange) && (!is_null($dateRange->getStart()) || !is_null($dateRange->getEnd())))
        {
            $dateStart = $dateRange->getStart();
            $dateEnd = $dateRange->getEnd();

            if(is_null($dateStart))
            {
                $where[] = ' i.pubDate<=\'' . $dateEnd->format('q Q', 0) . '\'';
            }
            else if(is_null($dateEnd))
            {
                $where[] = ' i.pubDate>=\'' . $dateStart->format('q Q', 0) . '\'';
            }
            else
            {
                $where[] = ' i.pubDate BETWEEN \'' . $dateEnd->format('q Q', 0)
                    . '\' AND \'' . $dateStart->format('q Q', 0) . '\'';
            }
        }

        $whereStatement = '';
        if(count($where) > 0)
        {
            $whereStatement = ' WHERE ' . implode(' AND ', $where);
        }

        $limitStatement = '';
        $count = intval($count);
        $start = intval($start);
        if($count > 0)
        {
            if($start < 0)
                $start = 0;

            $limitStatement = ' LIMIT ' . $start . ',' . $count;
        }

        $items = array();

        $query = new Query('SELECT i.* FROM ' . $db->getTable('Item') . ' AS i '
            . $keyNameJoin . $whereStatement
            . ' ORDER BY i.`pubDate` DESC' . $limitStatement);
        $result = $db->execQuery($query);
        while($row = $db->getAssoc($result))
        {
            $item = new AggItem();
            $item->setTitle($row['title']);
            $item->setLink($row['link']);
            $item->setDescription($row['description']);
            $item->setAuthor($row['author']);
            $item->setGUId($row['guid']);
            $item->setPublishDate(new Date($row['pubDate']));

            $items[] = $item;
        }

        return $items;
    }
}



?>
