<?php


require_once PATH_LIB . 'com/mephex/cache/InstanceCache.php';
require_once PATH_LIB . 'com/mephex/cache/MultiKeyInstanceCache.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/core/TimeDuration.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';
require_once PATH_LIB . 'com/mephex/data-object/DataField.php';
require_once PATH_LIB . 'com/mephex/data-object/event/DataObjectSaveEvent.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/AbstractFilter.php';
require_once PATH_LIB . 'com/mephex/data-object/form/DefaultForm.php';
require_once PATH_LIB . 'com/mephex/data-object/io/AbstractWriter.php';
require_once PATH_LIB . 'com/mephex/data-object/loader/DefaultLoader.php';


MXT_DataClass::initStaticVariables();


// added: 2009/03/15 20:52
// 2009/03/18 16:00 updated initFields() to detect time and date fields
abstract class MXT_DataClass
{
    private static $staticInitialized = false;
    private static $classInstances;

    protected $idField;
    protected $fields;

    protected $cachedAll;
    protected $cacheById;

    protected $loader;

    protected $reader;


    protected function __construct()
    {
        $this->idField = null;
        $this->fields = $this->initFields();

        $this->cachedAll = false;
        $this->cacheById = new MXT_InstanceCache();

        $this->loader = null;

        $this->reader = null;
    }


    public static function getSingletonUsingClassName($className)
    {
        $className = strtolower($className);
        if(class_exists($className))
        {
            $class = self::$classInstances->get($className);
            if(is_null($class))
            {
                $class = new $className();
                self::$classInstances->replace($className, $class);
            }

            return $class;
        }

        return null;
    }


    public abstract function getDataObjectName();


    public function getIdField()
    {
        return $this->idField;
    }


    public function getFields()
    {
        return $this->fields->getFields();
    }

    protected abstract function initFields();


    protected static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) == $needle;
    }


    public function getField($keyname)
    {
        $fields = $this->getFields();
        $field = Utility::getValueUsingKey($fields, $keyname);
        return $field;
    }

    public function isField($keyname)
    {
        $fields = $this->getFields();
        return array_key_exists($keyname, $fields);
    }




    public function getRowCount(MXT_DO_AbstractReader $reader = null)
    {
        if(is_null($reader))
            $reader = $this->getReader();

        return $reader->getTotalObjectCount();
    }

    public function getRowCountUsingFilter(MXT_DO_AbstractFilter $filter, MXT_DO_AbstractReader $reader = null)
    {
        if(is_null($reader))
            $reader = $this->getReader();

        return $reader->getTotalObjectCountUsingFilter($filter);
    }





    public function getObjectUsingId($id, MXT_DO_AbstractReader $reader = null)
    {
        if(!$this->cachedAll && !$this->getCache()->containsKey($id))
        {
            $this->queueObjectUsingId($id);
            $this->loadQueuedObjects($reader);
        }

        return $this->getCache()->get($id);
    }

    public function getAllObjects(MXT_DO_AbstractReader $reader = null)
    {
        if(is_null($reader))
            $reader = $this->getReader();

        if(!$this->cachedAll)
        {
            $reader->loadAllObjects();

            $this->cachedAll = true;
        }

        return $this->getCache()->getAll();
    }

    public function getObjectsUsingFilter(MXT_DO_AbstractFilter $filter, MXT_DO_AbstractReader $reader = null)
    {
        if(is_null($reader))
            $reader = $this->getReader();

        return $reader->getAllObjectsUsingFilter($filter);
    }


    public function getReader()
    {
        if(is_null($this->reader))
            $this->reader = $this->getDefaultReader();

        return $this->reader;
    }

    protected abstract function getDefaultReader();



    public function loadObjectsUsingIds(array $ids, MXT_DO_AbstractReader $reader = null)
    {
        if(is_null($reader))
            $reader = $this->getReader();

        $cache = $this->getCache();

        $notFoundIds = array();
        foreach($ids as $id)
        {
            if(!$cache->containsKey($id))
                $notFoundIds[$id] = $id;
        }

        $reader->loadObjectsUsingIds($notFoundIds);

        foreach($notFoundIds as $id)
        {
            if(!$cache->containsKey($id))
                $cache->replace($id, null);
        }
    }


    public function getCache()
    {
        return $this->cacheById;
    }



    public function getCreatedObject()
    {
        $objName = $this->getDataObjectName();
        $obj = new $objName($this);

        return $obj;
    }

    public function getNewObject()
    {
        $obj = $this->getCreatedObject();
        $this->initValuesUsingFields($obj);

        return $obj;
    }


    public function initLoader()
    {
        $this->loader = new MXT_DefaultLoader($this);
    }

    public function getLoader()
    {
        if(is_null($this->loader))
            $this->initLoader();

        return $this->loader;
    }


    public function queueObjectUsingId($id)
    {
        if(is_numeric($id) && $id > 0)
        {       
            #if(get_class($this) == 'LDS_DriverClass')
            #    {echo '<pre>'; debug_print_backtrace(); echo '</pre>';exit;}
            $this->getLoader()->queueObjectUsingId($id);
        }
    }

    public function loadQueuedObjects()
    {
        $this->getLoader()->loadQueuedObjects();
    }


    public function initValuesUsingFields(MXT_DataObject $obj)
    {
        $fields = $this->getFields();
        foreach($fields as $field)
        {
            #echo $field->getKeyname() . '<pre>'; var_dump($field->getDefaultValue());  echo '</pre>';
            $obj->setValue($field->getKeyname(), $field->getDefaultValue());
        }

        return $obj;
    }



    public function isObjectInstanceOf($value)
    {
        if($value instanceof MXT_DataObject)
        {
            return ($value->getDataClass() === $this);
        }

        return false;
    }





    public function saveUsingWriter(MXT_DataObject $obj, MXT_DO_AbstractWriter $writer, MXT_SaveListener $saveListener)
    {
        if($obj->isCreated())
        {
            $objClass = $this->getDataObjectName();
            $objSaved = $this->updateUsingWriter($obj, $writer);
            if($objSaved instanceof $objClass)
            {
                $saveListener->updateSucceeded(new MXT_DataObjectSaveEvent($objSaved));
                return true;
            }
            else
            {
                $saveListener->updateFailed(new MXT_DataObjectSaveEvent($objSaved));
                return false;
            }
        }
        else
        {
            $objClass = $this->getDataObjectName();
            $objSaved = $this->createUsingWriter($obj, $writer);
            if($objSaved instanceof $objClass)
            {
                $saveListener->createSucceeded(new MXT_DataObjectSaveEvent($objSaved));
                return true;
            }
            else
            {
                $saveListener->createFailed(new MXT_DataObjectSaveEvent($objSaved));
                return false;
            }
        }
    }

    public function createUsingWriter(MXT_DataObject $obj, MXT_DO_AbstractWriter $writer)
    {
        return $writer->create($obj);
    }

    public function updateUsingWriter(MXT_DataObject $obj, MXT_DO_AbstractWriter $writer)
    {
        return $writer->update($obj);
    }






    public static function initStaticVariables()
    {
        if(!self::$staticInitialized)
        {
            self::$classInstances = new MXT_InstanceCache();

            self::$staticInitialized = true;
        }
    }
}



?>
