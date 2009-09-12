<?php
die('this class was never completed. sorry.');

require_once PATH_LIB . 'com/mephex/cache/CacheableContent.php';
require_once PATH_LIB . 'com/mephex/cache/ContentCache.php';
require_once PATH_LIB . 'com/mephex/cache/InstanceCache.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/core/TimeDuration.php';
require_once PATH_LIB . 'com/mephex/data-object/class/CacheableDataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/DataField.php';
require_once PATH_LIB . 'com/mephex/data-object/form/DefaultForm.php';
require_once PATH_LIB . 'com/mephex/data-object/event/DataObjectSaveEvent.php';
require_once PATH_LIB . 'com/mephex/data-object/list/AbstractList.php';
require_once PATH_LIB . 'com/mephex/data-object/list/DefaultList.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DefaultType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DateType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/DateTimeType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/HiddenIdType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/NumericType.php';
require_once PATH_LIB . 'com/mephex/data-object/type/TimeDurationType.php';


abstract class MXT_IndexedDataClass extends MXT_CacheableDataClass
{
    protected $cachedFields;


    protected function __construct()
    {
        parent::__construct();

        $this->cachesByIndex = null;

        $this->indexes = null;
        $this->permutatedIndexes = null;

        echo '<pre>';
        print_r($this->getPermutatedIndexes());
        echo '</pre>';
    }


    public function getObjectUsingUniqueIndex(array $index)
    {

    }


    protected function initInstanceCaches()
    {
        $this->cachesByIndex = array();

        $indexes = $this->getIndexes();
        foreach($indexes as $index)
        {
            $keyname = $index->getKeyname();
            $dims = count($index->getFields());

            $this->cachesByIndex[$keyname] = new MXT_MultiKeyInstanceCache($dims);
        }
    }

    public function getInstanceCaches()
    {
        if(is_null($this->cachesByIndex))
            $this->initInstanceCaches();

        return $this->cachesByIndex;
    }


    protected function initIndexes()
    {
        $indexes = array();

        $tableName = $this->getTableName();
        $db = $this->getDbConnection();

        $query = new Query('SHOW INDEX FROM ' . $db->getTable($tableName));
        $result = $db->execQuery($query);
        while($row = $db->getAssoc($result))
        {
            //if($row['Non_unique'] == '0') {
                if($this->isField($row['Column_name']))
                {
                    $keyname = $row['Key_name'];
                    if(!array_key_exists($keyname, $indexes))
                    {
                        $type = MXT_TableIndex::TYPE_INDEX;
                        if(strtolower($row['Key_name']) == 'primary')
                            $type = MXT_TableIndex::TYPE_PRIMARY;
                        else if($row['Non_unique'] == '0')
                            $type = MXT_TableIndex::TYPE_UNIQUE;

                        $indexes[$keyname] = new MXT_TableIndex($keyname, $type);
                    }

                    $indexes[$keyname]->addField($row['Column_name']);
                }
            //}
        }

        $this->indexes = $indexes;
    }

    public function getIndexes()
    {
        if(is_null($this->indexes))
            $this->initIndexes();

        return $this->indexes;
    }


    protected function getPermutatedIndexes()
    {
        if(is_null($this->permutatedIndexes))
            $this->initPermutatedIndexes();

        return $this->permutatedIndexes;
    }

    protected function initPermutatedIndexes()
    {
        $permutated = array();

        $indexes = $this->getIndexes();
        foreach($indexes as $index)
        {
            $permutated = array_merge_recursive($permutated, $index->getPermutatedFields());
        }

        $this->permutatedIndexes = $permutated;
    }
}



?>
