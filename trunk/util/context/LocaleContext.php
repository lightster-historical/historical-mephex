<?php


require_once PATH_LIB . 'com/mephex/util/context/DefaultContext.php';


class MXT_LocaleContext extends MXT_DefaultContext
{
    protected $timezone;
    protected $language;


    public function __construct($timezone, $language)
    {
        $this->setTimezone($timezone);
        $this->setLanguage($language);
    }


    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getLanguage()
    {
        return $this->language;
    }


    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }
}


?>
