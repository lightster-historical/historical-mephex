<?php


/**
 * @import com.mephex.core.HttpHeader
 * @import com.mephex.framework.HttpError
 */
require_once PATH_LIB . 'com/mephex/core/Date.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';


class CacheableResponder extends HttpResponder
{
    protected $cacheDir;
    protected $cacheArgs;


    public function init($requestArgs, $cacheDir = null)
    {
        parent::init($requestArgs);

        $this->setCacheDir($cacheDir);
        $this->cacheArgs = array();
    }


    public function addCacheArg($value)
    {
        $this->cacheArgs[] = $value;
    }

    public function setCacheDir($cacheDir)
    {
        if(!is_null($cacheDir) && substr($cacheDir, -1) != '/')
            $cacheDir .= '/';

        $this->cacheDir = $cacheDir;
    }


    public function get($args)
    {
        $this->printHeader();

        if(!is_null($this->cacheDir))
        {
            $fileName = $this->getCacheFileName();

            $contentLastUpdated = $this->getContentLastUpdated();
            $cacheLastUpdated = $this->getCacheLastUpdated();

            if(file_exists($fileName)
                && (is_null($contentLastUpdated)
                    || $contentLastUpdated->compareTo($cacheLastUpdated) <= 0))
            {
                $fp = fopen($fileName, 'r');
                while(!feof($fp))
                    echo fread($fp, 1024);
                fclose($fp);
            }
            else
            {
                ob_start();
                $this->createCache($args);
                $txt = ob_get_contents();
                ob_end_clean();

                $fp = fopen($fileName, 'w');
                fwrite($fp, $txt);
                fclose($fp);

                echo $txt;
            }
        }
        else
        {
            $this->createCache($args);
        }

        $this->printFooter();
    }

    /*
    public function getAndCache($args)
    {
    }
    */

    public function createCache($args)
    {
    }


    public function getCacheFileName()
    {
        if(!is_null($this->cacheDir))
        {
            $cacheDir = $this->cacheDir;
            $cacheArgs = $this->cacheArgs;

            $fileName = $cacheDir . basename($_SERVER['PHP_SELF']) . '/';
            for($i = 0; $i < count($cacheArgs); $i++)
            {
                $fileName .= $cacheArgs[$i] . '/';
            }
            if(!file_exists($fileName))
                mkdir($fileName, 0777, true);
            $fileName .= 'cache.txt';

            return $fileName;
        }

        return null;
    }


    public function getContentLastUpdated()
    {
        return null;
    }

    public function getLastUpdated()
    {
        $fileName = $this->getCacheFileName();

        $contentLastUpdated = $this->getContentLastUpdated();
        $cacheLastUpdated = $this->getCacheLastUpdated();

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

    public function getCacheLastUpdated()
    {
        if(!is_null($this->cacheDir))
        {
            $fileName = $this->getCacheFileName();

            if(file_exists($fileName))
                return new Date(filemtime($fileName));
        }

        return null;
    }
}


?>
