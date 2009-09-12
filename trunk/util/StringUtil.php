<?php



class MXT_StringUtil
{
    public static function generateRandomStringUsingArray($length, array $chars)
    {
        $charCount = count($chars);

        $value = '';
        for($i = 0; $i < $length; $i++)
        {
            $value .= $chars[rand(1, $charCount) - 1];
        }

        return $value;
    }


    public static function generateRandomAlphaNumericString($length)
    {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        return self::generateRandomStringUsingArray($length, $chars);
    }

    public static function generateRandomUnambiguousString($length)
    {
        $chars = array_merge(range(2, 9), range('a', 'k'), range('m', 'z')
            , range('A', 'H'), range('J', 'N'), range('P', 'Z'));
        return self::generateRandomStringUsingArray($length, $chars);
    }
}



?>
