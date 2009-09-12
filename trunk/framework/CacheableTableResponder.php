<?php



/**
 * @import com.mephex.core.HttpHeader
 * @import com.mephex.framework.HttpError
 */
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/datastructures/TableCache.php';
require_once PATH_LIB . 'com/mephex/framework/CacheableResponder.php';



class CacheableTableResponder extends CacheableResponder
{
    protected $cacheDir;
    protected $cacheArgs;
    
    protected $sortCol;
    
    
    public function init($requestArgs, $cacheDir = null)
    {
        parent::init($requestArgs);
        
        $this->cacheDir = $cacheDir;
        $this->cacheArgs = array();
        $this->sortCol = -1;
    }
    
    
    public function addCacheArg($value)
    {
        $this->cacheArgs[] = $value;
    }
    
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }
    
    
    public function get($args)
    {
        $this->printHeader();
        
        $cacheDir = $this->cacheDir;
        $cacheArgs = $this->cacheArgs;
        
        $cache = null;
        if(!is_null($cacheDir))
        {
            $fileName = $cacheDir . basename($_SERVER['PHP_SELF']) . '/';
            for($i = 0; $i < count($cacheArgs); $i++)
            {
                $fileName .= $cacheArgs[$i] . '/';
            }
            if(!file_exists($fileName))
                mkdir($fileName, 0777, true);
            $fileName .= 'cache.txt';
            
            $cache = TableCache::read($fileName);
            if(is_null($fileName))
            {
                ob_start();
                $this->createCache($args);
                $html = ob_get_contents();
                $cache = new TableCache($html);
                ob_end_clean();
                
                unset($html);
                
                $cache->write($fileName);
            }
        }
        else
        {
            ob_start();
            $this->createCache($args);
            $html = ob_get_contents();
            $cache = new TableCache($html);
            ob_end_clean();
            
            unset($html);
        }
        
        $this->printCache($args, $cache);
        
        $this->printFooter();
    }
    
    public function printCache($requestArgs, TableCache $cache)
    {
    }
    
    public function createCache($args)
    {
    }
    
    public function getLastUpdated()
    {
        $cacheDir = $this->cacheDir;
        $cacheArgs = $this->cacheArgs;
        
        if(!is_null($cacheDir))
        {
            $fileName = $cacheDir . basename($_SERVER['PHP_SELF']) . '/';
            for($i = 0; $i < count($cacheArgs); $i++)
            {
                $fileName .= $cacheArgs[$i] . '/';
            }
            if(!file_exists($fileName))
                mkdir($fileName, 0777, true);
            $fileName .= 'cache.txt';
            
            return new Date(filemtime($fileName));
        }
        else
        {
            return null;
        }
    }
    
    public function printHeader()
    {
    }
    
    public function printFooter()
    {
    }
}



?>
