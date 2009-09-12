<?php


require_once PATH_LIB . 'com/mephex/core/Date.php';


class MXT_Captcha
{
    const DEFAULT_LENGTH = 7;
    const DEFAULT_EXPIRATION = 15;

    protected static $captchasById = array();

    protected $id;
    protected $value;



    protected function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function checkValue($userValue)
    {
        if($this->getValue() == $userValue)
            return true;

        return false;
    }

    public function destroyCaptcha()
    {
        self::destroyCaptchaUsingId($this->getId());
    }


    public static function generateNewCaptcha()
    {
        $date = new Date();
        $date->changeMinute(self::DEFAULT_EXPIRATION);

        return self::generateNewCaptchaWithExpirationAndLength($date, self::DEFAULT_LENGTH);
    }

    public static function generateNewCaptchaWithExpirationAndLength(Date $date, $length)
    {
        $db = Database::getConnection('com.mephex.captcha');

        $value = self::generateValue();

        $query = new Query('INSERT INTO ' . $db->getTable('Captcha')
            . ' (`value`, `expirationDate`) VALUES (\'' . addslashes($value)
            . '\', \'' . $date->format('q Q') . '\')');
        $db->execQuery($query);;
        $id = $db->getAutoIncrementId();

        $captcha = new MXT_Captcha($id, $value);
        self::$captchasById[$id] = $captcha;

        return $captcha;
    }

    public static function getCaptchaUsingId($id)
    {
        $captcha = null;

        if(array_key_exists($id, self::$captchasById))
            $captcha = self::$captchasById[$id];
        else
        {
            $db = Database::getConnection('com.mephex.captcha');

            $query = new Query('SELECT captchaId, value FROM '
                . $db->getTable('Captcha') . ' WHERE captchaId=' . intval($id));
            $result = $db->execQuery($query);
            if($row = $db->getRow($result))
            {
                $captcha = new MXT_Captcha($row[0], $row[1]);
                self::$captchasById[$row[0]] = $captcha;
            }
        }

        return $captcha;
    }

    public static function destroyCaptchaUsingId($id)
    {
        $db = Database::getConnection('com.mephex.captcha');

        $query = new Query('DELETE FROM ' . $db->getTable('Captcha')
            . ' WHERE captchaId=' . intval($id) . ' OR expirationDate<\''
            . Date::now('q Q') . '\'');
        $db->execQuery($query);

        if(array_key_exists($id, self::$captchasById))
        {
            unset(self::$captchasById[$id]);
        }
    }


    protected static function generateValue()
    {
        return self::generateValueWithLength(self::DEFAULT_LENGTH);
    }

    protected static function generateValueWithLength($length)
    {
        $chars = array_merge(range(2, 9), range('a', 'k'), range('m', 'z')
            , range('A', 'H'), range('J', 'N'), range('P', 'Z'));
        $charCount = count($chars);

        $value = '';
        for($i = 0; $i < $length; $i++)
        {
            $value .= $chars[rand(1, $charCount) - 1];
        }

        return $value;
    }
}


?>
