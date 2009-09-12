<?php


/*
MXT_MultiKeyInstanceCache is a multi-dimensional MXT_InstanceCache
*/
require_once PATH_LIB . 'com/mephex/cache/InstanceCache.php';


class MXT_MultiKeyInstanceCache
{
    protected $dimensions;
    protected $instances;


    public function __construct($dimensions)
    {
        $dimensions = intval($dimensions);

        if($dimensions <= 0)
            die('dummy, # of dims should be >0');

        $this->dimensions = $dimensions;

        if($dimensions == 1)
            $this->instances = new MXT_InstanceCache();
        else
            $this->instances = array();
    }


    protected function getDimensions()
    {
        return $this->dimensions;
    }


    public function add(array $key, $value)
    {
        list($cache, $k) = $this->getCacheAndKey($key);
        if(!is_null($cache) && !$cache->containsKey($k))
        {
            return $cache->add($k, $value);
        }

        return false;
    }

    public function replace(array $key, $value)
    {
        list($cache, $k) = $this->getCacheAndKey($key);
        $cache->replace($k, $value);
    }

    public function set(array $key, $value)
    {
        list($cache, $k) = $this->getCacheAndKey($key);
        $cache->set($k, $value);
    }


    protected function getInstanceCacheRecursive(array $keys, $create = true)
    {
        $k = array_shift($keys);

        if($this->getDimensions() == 1)
            return $this->instances;
        else
        {
            if(array_key_exists($k, $this->instances))
            {
                return $this->instances[$k]->getInstanceCacheRecursive($keys, $create);
            }
            else if($create)
            {
                $inst = new MXT_MultiKeyInstanceCache($this->getDimensions() - 1);
                $this->instances[$k] = $inst;
                return $inst->getInstanceCacheRecursive($keys, $create);
            }
            else
                return null;
        }
    }

    protected function getCacheAndKey(array $key, $create = true)
    {
        if(count($key) != $this->getDimensions())
            die('dummy, cache has ' . $this->getDimensions() . ' dims--not ' . count($key));

        $k = array_pop($key);

        return array($this->getInstanceCacheRecursive($key, $create), $k);
    }

    public function containsKey(array $key)
    {
        list($cache, $k) = $this->getCacheAndKey($key, false);
        if(is_null($cache))
            return false;
        else
            return $cache->containsKey($k);
    }


    public function get(array $key)
    {
        if($this->containsKey($key))
            return $this->instances[$key];

        return null;
    }

    public function getAll()
    {
        return $this->instances;
    }
}



?>
