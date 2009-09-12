<?php


require_once PATH_LIB . 'com/mephex/cache/InstanceCache.php';
require_once PATH_LIB . 'com/mephex/util/context/AbstractContextable.php';


class MXT_DefaultContext extends MXT_InstanceCache
{
    protected static $defaultContext;


    public function getValueOrDefault($key, $default = null)
    {
        if(!$this->containsKey($key))
            return $default;
        else
            return $this->get($key);
    }


    public static function getDefaultContext()
    {
        return self::$defaultContext;
    }


    public static function initStaticVariables()
    {
        self::$defaultContext = new MXT_DefaultContext();
    }
}


MXT_DefaultContext::initStaticVariables();


?>
