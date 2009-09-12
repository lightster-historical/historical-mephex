<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';


class MXT_Log
{
    protected static $logs = array();


    protected $keyname;
    protected $currentFile;

    protected $errorData;


    protected function __construct($keyname, $path)
    {
        $this->keyname = $keyname;

        if(!file_exists($path) && !@mkdir($path, 0777, true))
            die("Cannot write to $keyname log path: $path");

        $this->path = realpath($path);
        $this->currentFile = null;

        $this->errorData = '';
    }


    public static function setLog($keyname, $path)
    {
        $log = new MXT_Log($keyname, $path);
        self::$logs[$keyname] = $log;
    }

    public static function getLog($keyname)
    {
        if(array_key_exists($keyname, self::$logs))
        {
            return self::$logs[$keyname];
        }

        return null;
    }

    public static function isLogSet($keyname)
    {
        return array_key_exists($keyname, self::$logs);
    }


    public static function logException($keyname, Exception $ex)
    {
        $log = self::getLog($keyname);
        if(!is_null($log))
        {
            $log->writeException($ex);
        }
    }


    protected function getData()
    {
        $dateTime = Date::now('Y-m-d') . 'T' . Date::now('H:i:s') . 'Z';

        $data  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $data .= "<log>\n";
        $data .= " <created>$dateTime";
        $data .= "</created>\n";
        $data .= " <errors>\n";
        $data .= $this->errorData;
        $data .= " </errors>\n";
        $data .= "</log>\n";

        return $data;
    }


    protected function writeFile()
    {
        $data = $this->getData();

        $fileName =  $this->getCurrentFile();
        if($fp = @fopen($fileName, 'w'))
        {
            fwrite($fp, $data);
            fclose($fp);
        }
    }


    public function writeException(Exception $ex)
    {
        $this->addException($ex);
        $this->writeFile();
    }

    protected function addException(Exception $ex)
    {
        $data = '';
        $data .= "  <exception>\n";
        $data .= "   <message>" . $ex->getMessage() . "</message>\n";
        $data .= "   <number>" . $ex->getCode() . "</number>\n";
        $data .= "   <file>" . $ex->getFile() . "</file>\n";
        $data .= "   <line>" . $ex->getLine() . "</line>\n";
        $data .= $this->getTraceAsXML($ex);
        $data .= "  </exception>\n";

        $this->errorData .= $data;
    }


    protected function getTraceAsXML(Exception $ex)
    {
        $depth = 1;
        $trace = $ex->getTrace();

        $data  = "   <trace>\n";
        foreach($trace as $call)
        {
            $data .= $this->getTraceCallAsXML($call);
        }
        $data .= "   </trace>\n";

        return $data;
    }

    protected function getTraceCallAsXML(array $call)
    {
        $data  = "    <call>\n";
        foreach($call as $elem => $value)
        {
            if(is_object($value))
                $value = get_class($value) . ' Object';
            else if(is_array($value))
            {
                if($elem == 'args')
                    $value = $this->getTraceCallArgsAsXML($value);
                else
                    $value = 'Array';
            }

            $data .= "     <$elem>$value</$elem>\n";
        }
        $data .= "    </call>\n";

        return $data;
    }

    protected function getTraceCallArgsAsXML(array $args)
    {
        $data  = "\n";
        foreach($args as $value)
        {
            if(is_object($value))
            {
                $value = get_class($value) . ' Object';
                if($value instanceof Query)
                    $value .= ': ' . $value->getQuery();
            }
            else if(is_array($value))
                $value = 'Array';

            $data .= "      <arg>$value</arg>\n";
        }
        $data .= '     ';

        return $data;
    }




    public function getRootPath()
    {
        return $this->path;
    }

    protected function getCurrentFile()
    {
        if(is_null($this->currentFile))
        {
            $time = explode(' ', microtime());
            $frac = sprintf('%04s', intval(floatval($time[0])*10000));

            $dir = $this->getRootPath() . Date::now('/Y/m/d/', 0);
            $file = $dir . Date::now('H.i.s.') . $frac . '.xml';

            if(!file_exists($dir) && !@mkdir($dir, 0777, true)
                && is_writeable($file))
                die("Cannot write to $keyname log path: $file");

            $this->currentFile = $file;
        }

        return $this->currentFile;
    }




    //gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . 'Z'
}



?>
