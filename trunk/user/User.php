<?php



require_once PATH_LIB . 'com/mephex/captcha/Captcha.php';
require_once PATH_LIB . 'com/mephex/core/Cookie.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/db/Database.php';
require_once PATH_LIB . 'com/mephex/db/Query.php';
require_once PATH_LIB . 'com/mephex/input/EmailInput.php';
require_once PATH_LIB . 'com/mephex/input/FormInputsException.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';
require_once PATH_LIB . 'com/mephex/user/Group.php';
require_once PATH_LIB . 'com/mephex/user/Permission.php';
require_once PATH_LIB . 'com/mephex/user/PermissionDomain.php';



class User
{
    protected static $activeUser = null;

    protected $userId;
    protected $username;
    protected $password;
    protected $securityHash;
    protected $groups;

    protected $timeZone;

    protected $permissions;
    protected $permissionsInherited;
    protected $permissionValues;



    private function __construct($username, $password, $securityHash)
    {
        $this->userId = null;
        $this->username = $username;
        $this->password = $password;
        $this->securityHash = $securityHash;
        $this->groups = array();

        $this->timeZone = 0;

        $this->permissions = array();
        $this->permissionsInherited = array();
        $this->permissionValues = array();
    }



    public static function getUser($userId)
    {
        $db = Database::getConnection('com.mephex.user');

        $user = null;

        $userId = intval($userId);
        if($userId > 0)
        {
            $query = new Query('SELECT u.userId, u.username, u.password, u.securityHash, u.timeZone, GROUP_CONCAT(ug.groupId SEPARATOR \',\') FROM '
                . $db->getTable('user') . ' AS u LEFT JOIN user_userGroup AS ug ON u.userId=ug.userId WHERE u.userId=' . $userId . ' GROUP BY u.userId');
            $result = $db->execQuery($query);

            if($row = $db->getRow($result))
            {
                $user = new User($row[1], $row[2], $row[3]);
                $user->setId($row[0]);
                $user->timeZone = $row[4];

				$groups = explode(',', $row[5]);
				foreach($groups as $groupId)
					$user->groups[$groupId] = Group::getGroup($groupId);
				/*
                $query = new Query('SELECT groupId FROM user_userGroup '
                    . 'WHERE userId=' . $userId);
                $result = $db->execQuery($query);
                while($row = $db->getRow($result))
                    $user->groups[$row[0]] = Group::getGroup($row[0]);
				*/

                $query = new Query('SELECT pd.domain, p.index, up.value '
                    . 'FROM user_permission AS up '
                    . 'INNER JOIN permission AS p ON up.permissionId=p.permissionId '
                    . 'INNER JOIN permissionDomain AS pd ON p.domainId=pd.domainId '
                    . 'WHERE userId=' . $userId);
                $result = $db->execQuery($query);
                while($row = $db->getRow($result))
                    $this->permissionValues[$row[0]][$row[1]] = $row[2];
            }
        }

        return $user;
    }


    public function getPermission($domain, $keyName, $allowInherit = true)
    {
        $pDomain = PermissionDomain::getInstance($domain);

        $index = null;
        if(!is_null($pDomain))
            $index = $pDomain->getIndex($keyName);

        if(!is_null($index))
        {
            if($index < 0)
            {
                return $pDomain->getPermission($keyName);
            }
            else if($allowInherit
                && array_key_exists($domain, $this->permissionsInherited)
                && array_key_exists($index, $this->permissionsInherited[$domain]))
            {
                return $this->permissionsInherited[$domain][$index];
            }
            else if(array_key_exists($domain, $this->permissions)
                && array_key_exists($index, $this->permissions[$domain]))
            {
                if(!$allowInherit)
                    return $this->permissions[$domain][$index];
                else
                {
                    $permission = $this->permissions[$domain][$index];
                    if(is_null($permission))
                    {
                        foreach($this->groups as $group)
                        {
                            $permission = Permission::merge($permission
                                , $group->getPermission($domain, $keyName, false));

                            if($permission === false)
                                break;
                        }

                        if(is_null($permission))
                        {
                            $permission = $pDomain->getPermission($keyName);
                        }
                    }

                    $this->permissionsInherited[$domain][$index] = $permission;

                    return $this->getPermission($domain, $keyName, $allowInherit);
                }
            }
            else if(array_key_exists($domain, $this->permissionValues))
            {
                $digit = $index % 5;
                $group = (($index - $digit) / 5) + 1;

                $value = $this->permissionValues[$domain][$group];
                $values = Permission::decode($value);

                for($i = 0; $i < 5; $i++)
                {
                    $this->permissions[$domain][$group * 5 + $i - 5] = $values[$i];
                }

                return $this->getPermission($domain, $keyName, $allowInherit);
            }
            else
            {
                $digit = $index % 5;
                $group = (($index - $digit) / 5) + 1;

                $this->permissionValues[$domain][$group] = Permission::INHERIT_ALL;

                return $this->getPermission($domain, $keyName, $allowInherit);
            }
        }

        return null;
    }




    public function saveUser(Database $db)
    {
        if(!is_null($this->getId()))
        {
            $query = new Query('UPDATE ' . $db->getTable('user') . ' SET '
                . 'userName=\'' . addslashes($this->getUserName())
                . '\', passwordHash=\'' . addslashes($this->getPassword())
                . '\' WHERE userId=' . $this->getId());
            return $db->execQuery() ? true : false;
        }
        else
        {
            $query = new Query('UPDATE ' . $db->getTable('user') . ' SET '
                . 'userName=\'' . addslashes($this->getUserName())
                . '\', passwordHash=\'' . addslashes($this->getPassword())
                . '\' WHERE userId=' . $this->getId());
            return $db->execQuery() ? true : false;
        }
    }

    private function setId($userId)
    {
        $this->userId = $userId;
    }

    public function setUserName($userName)
    {
        $where = 'userName=\'' . addslashes($userName) . '\'';
        if(!is_null($this->getId()))
        {
            $where .= ' AND userId=' . $this->getId();
        }

        $query = new Query('SELECT COUNT(userId) FROM '
            . $db->getTable('user') . ' WHERE ' . $where);
        $result = $db->execQuery($query);
        $row = $db->getRow($result);

        if($row && $row[0] <= 0)
        {
            $this->userName = $userName;
            return true;
        }
        else
        {
            // ExceptionNeeded
            return false;
        }
    }

    public function setPassword($password)
    {
        $db = Database::getConnection('com.mephex.user');

        $password = md5($password);

        $query = new Query('UPDATE user SET password=\''
            . md5($password . $this->securityHash) . '\', passwordDate=\''
            . Date::now('q Q') . '\' WHERE userId=' . $this->getId());
        if($db->execQuery($query))
        {
            $this->password = md5($password . $this->securityHash);
        }
    }

    public function setTimeZone($timeZone)
    {
        $db = Database::getConnection('com.mephex.user');

        $query = new Query('UPDATE user SET timeZone=\''
            . $timeZone . '\' WHERE userId=' . $this->getId());
        if($db->execQuery($query))
        {
            $this->timeZone = $timeZone;
        }
    }

    public function setTimeDifference($serverTime, $clientTime)
    {
        $diff = $clientTime - $serverTime;
        $hours = round(($diff + date('Z')) / 3600, 2);
        $this->setTimeZone($hours);
    }


    public function getId()
    {
        return $this->userId;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function verifyPassword($password, $md5ed = true)
    {
        if(!$md5ed)
            $password = md5($password);

        return md5($password . $this->securityHash) == $this->password;
    }

    public function getTimeZone()
    {
        return $this->timeZone;
    }



    public static function getActiveUser($userId = null, $password = null)
    {
        if(!is_null(self::$activeUser) && is_null($userId) && is_null($password))
        {
            return self::$activeUser;
        }
        else
        {
            $db = Database::getConnection('com.mephex.user');

            if(is_null($userId) || is_null($password))
            {
                $userId = Cookie::getInstance()->get('userId');
                $password = Cookie::getInstance()->get('password');
            }

            $user = User::getUser($userId);
            if(!is_null($user) && $user->verifyPassword($password))
            {
                $query = new Query('UPDATE user SET lastActive=\''
                    . Date::now('q Q', 0) . '\' WHERE userId=' . $user->getId());
                $result = $db->execQuery($query);

                self::$activeUser = &$user;

                return $user;
            }
        }

        return null;
    }

    public static function setActiveUser($username, $password)
    {
        $db = Database::getConnection('com.mephex.user');

        $password = md5($password);

        $query = new Query('SELECT userId FROM user WHERE '
            . 'username=\'' . addslashes($username) . '\' AND '
            . 'password=MD5(CONCAT(\'' . addslashes($password) . '\', '
            . 'securityHash))');
        $result = $db->execQuery($query);
        $row = $db->getRow($result);
        if($row[0] > 0)
        {
            Cookie::getInstance()->set('userId', $row[0]);
            Cookie::getInstance()->set('password', $password);

            return self::getActiveUser($row[0], $password);
        }

        return null;
    }

    public static function clearActiveUser()
    {
        Cookie::getInstance()->delete('userId');
        Cookie::getInstance()->delete('password');
    }


    public static function createUsingFormInput($groupIds, Input $input)
    {
        $db = Database::getConnection('com.mephex.user');

        $input->set('username');
        $input->set('first_name');
        $input->set('last_name');
        $input->set('email');
        $input->set('email_confirm');
        $input->set('password');
        $input->set('password_confirm');
        $input->set('timezone', IntegerInput::getInstance());
        $input->set('captcha_value');
        $input->set('captcha', IntegerInput::getInstance());

        $username = trim($input->get('username'));
        $firstName = trim($input->get('first_name'));
        $lastName = trim($input->get('last_name'));
        $email = trim($input->get('email'));
        $emailConfirm = trim($input->get('email_confirm'));
        $password = trim($input->get('password'));
        $passwordConfirm = trim($input->get('password_confirm'));
        $timezone = $input->get('timezone');

        $captchaId = $input->get('captcha');
        $captcha = MXT_Captcha::getCaptchaUsingId($captchaId);
        $captchaValue = trim($input->get('captcha_value'));

        $errors = array();

        if($username == '')
            $errors['username'] = 'A username is required.';
        if($firstName == '')
            $errors['first_name'] = 'Your first name is required.';
        if($lastName == '')
            $errors['last_name'] = 'The first initial of your last name is required.';

        $emailValidator = EmailInput::getInstance();
        if($email == '')
            $errors['email'] = 'Your e-mail address is required.';
        else if(!$emailValidator->isValid($email))
            $errors['email'] = 'This does not appear to be a valid e-mail address.';
        else if($emailConfirm == '')
            $errors['email_confirm'] = 'Please re-enter your e-mail address.';
        else if($email != $emailConfirm)
            $errors['email'] = 'The e-mail addresses provided do not match.';

        if($password == '')
            $errors['password'] = 'A password is required.';
        else if(strlen($password) < 6)
            $errors['password'] = 'You must provide a password of 6 characters or more.';
        else if($passwordConfirm == '')
            $errors['password'] = 'Please re-enter your password and password confirmation.';
        else if($password != $passwordConfirm)
            $errors['password'] = 'The passwords provided do not match.';

        if(count($errors) > 0)
            $errors['captcha'] = 'Please enter the new verification code.';
        else if($captchaValue == '')
            $errors['captcha'] = 'Please enter the verification code.';
        else if(is_null($captcha) || !$captcha->checkValue($captchaValue))
            $errors['captcha'] = 'The entered code does not match the verification code.';

        if(count($errors) > 0 && !array_key_exists('password', $errors))
            $errors['password'] = 'Please re-enter your password and password confirmation.';

        if(count($errors) > 0)
            throw new FormInputsException($errors, null);



        return null;
        return self::setActiveUser($username, $password);
    }
}


?>
