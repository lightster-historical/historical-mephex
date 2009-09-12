<?php


require_once PATH_LIB . 'com/mephex/dev/Debug.php';


class MXT_LanguageGroup
{
    protected $keyname;
    protected $groups;
    protected $statement;


    public function __construct($keyname)
    {
        $this->keyname = $keyname;
        $this->groups = array();
        $this->statement = null;
    }


    public function getKeyname()
    {
        return $this->keyname;
    }


    public function get($group, $createOnMissing = true)
    {
        $groups = explode('.', $group, 2);

        $keyname = trim($groups[0]);
        $remains = null;
        if(count($groups) >= 2)
            $remains = $groups[1];

        if($keyname == '')
            return $this;
        else if(array_key_exists($keyname, $this->groups) || $createOnMissing)
        {
            if(!array_key_exists($keyname, $this->groups))
                $this->groups[$keyname] = new MXT_LanguageGroup($keyname);

            if(!is_null($remains))
                return $this->groups[$keyname]->get($remains, $createOnMissing);

            return $this->groups[$keyname];
        }
        else
        {
            return null;
        }
    }


    public function getStatement($key, $nullOnMissing = false)
    {
        $group = $this->get($key, false);
        if(!is_null($group))
            return $group->statement;
        else if(!$nullOnMissing)
            return MXT_Debug::passthru('mephex.lang', '[language:statement="' . $key .'"]');

        return null;
    }

    public function getParsedStatement($key)
    {
        $args = func_get_args();
        unset($args[0]);

        return $this->getParsedStatementUsingArray($key, $args);
    }

    public function getParsedStatementUsingArray($key, $args)
    {
        $statement = $this->getStatement($key);
        if(!is_null($statement))
            return vsprintf($key, $args);

        return null;
    }


    public function setStatement($key, $value)
    {
        $group = $this->get($key, true);
        $group->statement = $value;
    }
}


?>
