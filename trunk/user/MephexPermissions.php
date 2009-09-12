<?php



require_once PATH_LIB . 'com/mephex/user/PermissionDomain.php';



class MephexPermissions extends PermissionDomain
{
    protected static $instance = null;



    protected function __construct()
    {
        parent::__construct('com.mephex');
    }

    protected function setKeyNames()
    {
        $this->setKeyName(-1, 'is-user');
        $this->setKeyName(-1, 'is-admin');
    }


    public static function getInstance($class = null)
    {
        if(is_null(self::$instance))
            self::$instance = new MephexPermissions();

        return self::$instance;
    }


    public function getPermission($keyName)
    {
        if($keyName == 'is-user' || $keyName == 'is-guest')
        {
            if(!is_null(User::getActiveUser()))
                return $keyName == 'is-user';
            return $keyName == 'is-guest';
        }

        return parent::getPermission($keyName);
    }
}



?>
