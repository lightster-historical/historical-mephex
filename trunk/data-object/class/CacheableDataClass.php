<?php


require_once PATH_LIB . 'com/mephex/cache/CacheableContent.php';
require_once PATH_LIB . 'com/mephex/cache/ContentCache.php';
require_once PATH_LIB . 'com/mephex/cache/InstanceCache.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/data-object/class/DataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/field/Fields.php';


abstract class MXT_CacheableDataClass extends MXT_DataClass implements MXT_CacheableContent
{
    protected $cachedFields;


    protected function __construct($timezone = 0)
    {
        $this->cachedFields = null;

        parent::__construct($timezone);
    }


    protected function initFields()
    {
        $cache = new MXT_ContentCache();

        $fields = new MXT_DO_Fields($this);

        if($cache->isCacheCurrent($this))
        {
            //echo 'cached';
            require_once $cache->getCacheFileName($this);
        }
        else
        {
            //echo 'uncached';
            $fields = $this->getCachedFields();
            $cache->createCache($this);
        }

        return $fields;
    }


    protected abstract function getCacheableFields();


    public function getCachedFields()
    {
        if(is_null($this->cachedFields))
        {
            $this->cachedFields = $this->getCacheableFields();
        }

        return $this->cachedFields;
    }


    public function getContent()
    {
        $fields = $this->getCachedFields()->getFields();

        $code  = "<?php\n";
        $code .= "if(isset(\$this) && \$this instanceof MXT_CacheableDataClass)\n";
        $code .= "{\n";
        foreach($fields as $field)
        {
            $code .= $field->getFieldCacheCode() . "\n";
        }

        $idField = $this->getIdField();
        if(!is_null($idField))
            $code .= "\n    " . '$this->idField = $fields->get(\'' . $idField->getKeyname() . '\');' . "\n";

        $code .= "}\n";
        $code .= '?>';

        return $code;
    }

    public function getContentLastUpdated()
    {
        $classModified = new Date(filemtime($this->getClassFileName()));
        $thisModified = new Date(filemtime(__FILE__));

        #return new Date();
        return Date::getMax($classModified, $thisModified);
    }

    public function getDirectory()
    {
        return 'com/mephex/data-object/class';
    }

    public function getFileName()
    {
        $root = realpath(PATH_LIB . '/');
        $file = realpath($this->getClassFileName());

        return substr($file, strlen($root));
    }


    public abstract function getClassFileName();
}



?>
