<?php


require_once PATH_LIB . 'com/mephex/cache/CacheableContent.php';
require_once PATH_LIB . 'com/mephex/core/Date.php';


class MXT_ContentCache
{
    protected $cacheRoot;


    public function __construct($cacheRoot)
    {
        $this->setCacheRoot($cacheRoot);
    }


    protected function setCacheRoot($cacheRoot)
    {
        if(!is_null($cacheRoot) && substr($cacheRoot, -1) != '/')
            $cacheRoot .= '/';

        $this->cacheRoot = $cacheRoot;
    }


    public function get(MXT_CacheableContent $cacheable)
    {
        if(!is_null($this->getCacheRoot()))
        {
            if($this->isCacheCurrent($cacheable))
            {
                return $this->getFromCache($cacheable);
            }
            else
            {
                return $this->createCache($cacheable);
            }
        }

        return null;
    }


    public function isCacheCurrent(MXT_CacheableContent $cacheable)
    {
        $fileName = $this->getCacheFileName($cacheable);

        $contentLastUpdated = $cacheable->getContentLastUpdated();
        $cacheLastUpdated = $this->getCacheLastUpdated($cacheable);

        return file_exists($fileName) && filesize($fileName) > 0
            && (is_null($contentLastUpdated)
                || $contentLastUpdated->compareTo($cacheLastUpdated) <= 0);
    }


    public function createCache(MXT_CacheableContent $cacheable)
    {
        $fileName = $this->getCacheFileName($cacheable);

        ob_start();
        $isContent = $cacheable->getContent();
        $content = ob_get_contents();
        ob_end_clean();

        if($isContent != '')
            $content = $isContent;

        if($fp = @fopen($fileName, 'w'))
        {
            fwrite($fp, $content);
            fclose($fp);
        }
        else
        {
            echo 'Could not write to cache file: ' . $fileName;
        }

        return $content;
    }

    public function getFromCache(MXT_CacheableContent $cacheable)
    {
        if(!is_null($this->getCacheRoot()))
        {
            $fileName = $this->getCacheFileName($cacheable);

            if(file_exists($fileName))
            {
                $fp = fopen($fileName, 'r');
                $text = fread($fp, filesize($fileName));
                fclose($fp);

                return $text;
            }
        }

        return null;
    }


    public function getCacheRoot()
    {
        return $this->cacheRoot;
    }

    public function getCacheFileName(MXT_CacheableContent $cacheable)
    {
        $cacheRoot = $this->getCacheRoot();
        if(!is_null($cacheRoot))
        {
            $cacheDir = $cacheRoot . $cacheable->getDirectory();
            if(strlen($cacheDir) > 0 && substr($cacheDir, -1) != '/')
                $cacheDir .= '/';

            $cacheArgs = $cacheable->getFileName();
            if(!is_array($cacheArgs))
                $cacheArgs = array($cacheArgs);

            $fileName = $cacheDir;
            $separator = '';
            foreach($cacheArgs as $cacheArg)
            {
                $fileName .= $separator . $cacheArg;
                $separator = '/';
            }

            $cacheDir = dirname($fileName);
            if(!file_exists($cacheDir))
            {
                if(!@mkdir($cacheDir, 0777, true))
                    echo 'Could not create cache directory: ' . $cacheDir;
            };

            return $fileName;
        }

        return null;
    }


    public function getLastUpdated(MXT_CacheableContent $cacheable)
    {
        $fileName = $this->getCacheFileName($cacheable);

        $contentLastUpdated = $cacheable->getLastUpdated();
        $cacheLastUpdated = $this->getCacheLastUpdated($cacheable);

        if(file_exists($fileName)
            && (is_null($contentLastUpdated)
                || $contentLastUpdated->compareTo($cacheLastUpdated) <= 0))
        {
            return $cacheLastUpdated;
        }
        else
        {
            return $contentLastUpdated;
        }
    }

    public function getCacheLastUpdated(MXT_CacheableContent $cacheable)
    {
        if(!is_null($this->getCacheRoot()))
        {
            $fileName = $this->getCacheFileName($cacheable);

            if(file_exists($fileName))
                return new Date(filemtime($fileName));
        }

        return null;
    }
}


?>
