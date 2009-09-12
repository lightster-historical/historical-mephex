<?php


require_once PATH_LIB . 'com/mephex/core/Utility.php';
require_once PATH_LIB . 'com/mephex/language/LanguageGroup.php';


class MXT_Language
{
    protected static $languageStack = array();
    protected static $languages = array();

    protected static $groupStack = array();

    protected static $currLanguage = null;
    protected static $currGroup = null;


    protected $langCode;
    protected $groups;


    protected function __construct($langCode)
    {
        $this->langCode = $langCode;
        $this->groups = new MXT_LanguageGroup('');
    }


    protected static function getLanguage($langCode)
    {
        if(is_null($langCode))
            return null;
        else if(!array_key_exists($langCode, self::$languages))
            self::$languages[$langCode] = new MXT_Language($langCode);

        return self::$languages[$langCode];
    }

    protected static function getCurrentLanguage()
    {
        return self::$currLanguage;
    }


    protected static function getGroup($group)
    {
        $lang = self::getCurrentLanguage();
        if(is_null($lang))
            throw new Exception("No language selected.");

        return $lang->groups->get($group);
    }

    protected static function getCurrentGroup()
    {
        return self::$currGroup;
    }


    public static function loadFile($path)
    {
        $lang = self::getCurrentLanguage();
        if(is_null($lang))
            throw new Exception("No language selected.");

        $langCode = $lang->langCode;
        $path = PATH_LIB . $path . '/lang/' . $langCode . '.php';
        if(Utility::verifyLibraryPath($path))
            require_once $path;
        else
            throw new Exception("Language file not found:" . $path);
    }


    public static function pushLanguage($langCode)
    {
        self::$currLanguage = self::getLanguage($langCode);
        self::$languageStack[] = self::$currLanguage;
    }

    public static function popLanguage()
    {
        array_pop(self::$languageStack);
        self::$currLanguage = end(self::$languageStack);

        if(!self::$currLanguage)
            self::$currLanguage = null;
    }


    public static function pushGroup($group)
    {
        $first = substr($group, 0, 1);
        if($first == '.')
        {
            $group = substr($group, 1);

            $currGroup = self::getCurrentGroup();
            if(is_null($currGroup))
                self::$currGroup = self::getGroup($group);
            else
                self::$currGroup = $currGroup->get($group);
        }
        else
        {
            self::$currGroup = self::getGroup($group);
        }

        self::$groupStack[] = self::$currGroup;
    }

    public static function popGroup()
    {
        array_pop(self::$groupStack);
        self::$currGroup = end(self::$groupStack);

        if(!self::$currGroup)
            self::$currGroup = null;
    }



    public static function getStatement($key, $nullOnMissing = false)
    {
        $group = self::getCurrentGroup();
        if(is_null($group))
            throw new Exception("No language group selected.");

        return $group->getStatement($key, $nullOnMissing);
    }

    public static function getParsedStatement($key)
    {
        $args = func_get_args();
        unset($args[0]);

        return self::getParsedStatementUsingArray($key, $args);
    }

    public static function getParsedStatementUsingArray($key, $args)
    {
        $group = self::getCurrentGroup();
        if(!is_null($group))
            return $group->getParsedStatementUsingArray($key, $args);

        return null;
    }


    public static function getStatementOrBackup($key, $backup, $nullOnMissing = false)
    {
        $statement = self::getStatement($key, true);
        if(is_null($statement))
        {
            self::pushGroup($backup);
            $statement = self::getStatement('', $nullOnMissing);
            self::popGroup();
        }

        return $statement;
    }


    public static function setStatement($key, $value)
    {
        $group = self::getCurrentGroup();
        if(!is_null($group))
            return $group->setStatement($key, $value);

        return false;
    }
}


?>
