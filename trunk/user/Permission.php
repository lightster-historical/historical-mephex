<?php



require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';



class Permission
{
    const DENY_ALL = 0;
    const GRANT_ALL = 121;
    const INHERIT_ALL = 242;



    public static function encode($values)
    {
        if(is_array($values))
        {
            $value = 0;
            $x = 1;
            foreach($values as $key => $val)
            {
                if(0 <= $key && $key < 5)
                {
                    $value += $val * $x;
                    $x *= 3;
                }
            }

            return $value;
        }

        return 0;
    }

    public static function decode($value)
    {
        if(!is_array($value))
        {
            $options = array(false, true, null);

            $values = array();
            for($i = 0; $i < 5; $i++)
            {
                $remainder = $value % 3;
                $values[$i] = $options[$remainder];

                $value = ($value - $remainder) / 3;
            }

            return $values;
        }

        return $value;
    }


    public static function merge($a, $b)
    {
        if($a === true || $b === true)
            return true;
        else if($a === false || $b === false)
            return false;

        return null;
    }


    public static function getPermission($user, $domain, $keyName)
    {
        if(!is_null($user))
            return $user->getPermission($domain, $keyName);
        else
        {
            $pDomain = PermissionDomain::getInstance($domain);

            if(!is_null($pDomain))
                return $pDomain->getPermission($keyName);
        }

        return null;
    }
}


?>
