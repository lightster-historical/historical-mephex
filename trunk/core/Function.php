<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 *
 *
 * PHP version 5
 *
 * LICENSE: This file may only be used under the terms of the license
 *          available at www.mephex.com.
 *
 * @author     Matt Light <mlight@@mephex..com>
 * @copyright  2006 Mephex Technologies
 * @license    http://www.mephex.com
 * @link
 * @since      05.12.19
 * @version    05.12.22
 */
 
 
// if the array_key_exists function does not exist
if (!function_exists('array_key_exists'))
{
    /**
     * returns true if the key exists in the array or false otherwise
     *
     * @since      05.12.19
     * @version    05.12.22
     *
     * @param
     *     mixed   key     the key to find in the array
     *     array   array   the array to search in
     * @return bool
     *     true    the key exists
     *     false   the key does not exist
     */
    function array_key_exists ($key, $array)
    {
        return in_array($key, array_keys($array));
    }
    // array_key_exists function
}


/**
 * returns a string with another string inserted
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     string  subj        original string
 *     string  ins         string to insert
 *     integer start       position in the original string to start inserting 
 *                         the string
 *     integer repl = 0    number of characters to replace
 * @return string  result  the original string with the other string inserted
 */
function strIns ($subj, $ins, $start, $repl = 0)
{
    return substr($subj, 0, $start) . $ins . substr($subj, $start + $repl);
}
// strIns function


/**
 * @since      05.12.19
 * @version    05.12.19
 */
function implodeCast ($pieces, $func = 'intval', $glue = ',')
{
    // if an array was provided
    if (is_array($pieces) && count($pieces) > 0)
    {
        // the result starts empty
        $result = '';
        $delim  = '';

        foreach ($pieces as $piece)
        {
            // cast the piece
            $piece = $func($piece);
            
            // if the piece is numeric, do not use quotes
            if (is_numeric($piece))
            {
                $result .= $delim . $piece;
            }
            // if the piece is not numeric, use quotes
            else
            {
                $result .= $delim . '\'' . $piece . '\'';
            }

            // set the separator
            $delim   = $glue;
        }
    }
    // otherwise, return null
    else
    {
        $result = null;
    }

    return $result;
}
// implodeCast function

/**
 * @since      05.12.27
 * @version    05.12.27
 */
function intVals ($vals) 
{
    if (is_array($vals))
    {
        $list = $vals;
    }
    else
    {
        $list = explode(',', $vals);
    }
    
    $labels = array();
    foreach ($list as $label)
    {
        $range = explode('-', $label);
        
        if (count($range) > 2)
        {
            $min = max(min($range), 1);
            $max = max(max($range), 1);
            
            for ($cnt = $min; $cnt <= $max; $cnt++)
            {
                $labels[] = $cnt;
            }
        }
        else
        {
            $label = intval($label);
            
            if ($label > 0)
            {
                $labels[] = $label;
            }
        }
    }
    
    return $labels;
}
// intVals method


/**
 * forwards the browser to another page and exits the running of the rest of the
 * program
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     string  url             the location to send the browser to
 *     bool    exit = true     whether or not to exit the program
 * @return void
 */
function forward ($url, $exit = true)
{
    // forward the browser to the url
    header('Location:' . $url);

    // exit the program
    if ($exit)
    {
        exit;
    }
}
// forward function


/**
 * takes two or three arguments. In the case of two arguments, iif returns the
 * first non-empty argument. In the case of three arguments, the third argument 
 * is returned if argument one equals argument two, and returns argument two in 
 * any other case.
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     mixed   one
 *     mixed   two
 *     mixed   three
 * @return mixed
 */
function iif ($one, $two, $three = null)
{
    // if a third argument is provided
    if (!is_null($three))
    {
        // if the first and second argument are equal, return the third argument
        if ($one == $two)
        {
            return $three;
        }
        // otherwise, return the second argument
        else
        {
            return $two;
        }
    }
    // otherwise
    else
    {
        // if the first argument is non-empty, return the first argument
        if (!empty($one) || $one === 0)
        {
            return $one;
        }
        // otherwise, return the second argument
        else
        {
            return $two;
        }
    }
}
// iif function



/**
 * returns a random number between min and max
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     integer min     the minimum integer that may be returned
 *     integer max     the maximum integer that may be returned
 * @return integer     a random integer
 */
function random ($min, $max)
{
    // if the random number generator has not been seeded
    if (!isset($seeded))
    {
        // seed the number generator and record that the generator has been
        // seeded
        mt_srand(doubleval(microtime()) * 1000000);
        static $seeded = true;
    }

    // if min is greater than max, switch the variables
    if ($min > $max)
    {
        $min = $min ^ $max;
        $max = $min ^ $max;
        $min = $min ^ $max;
    }

    // return a random number
    return mt_rand(intval($min), intval($max));
}
// random function

/**
 * returns a random string
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     integer len = 20    the length the random string should be
 *     string  chars       string of characters to be used in the random string
 * @return string  a random string
 */
function randomStr ($length = 20,
    $chars = '123456789123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ')
{
    // the number of possible characters that may be used when generating the
    // random string
    $charCnt = strlen($chars) - 1;

    $str = '';

    // add random characters to the string
    for ($lttr = 1; $lttr <= $length; $lttr++)
    {
        $str    .= $chars[random(0, $charCnt)];
    }

    // return the string
    return $str;
}
// randomStr function


/**
 * returns the argument with slashes removed
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param  mixed   string or array that contains slashes that should be stripped
 * @return mixed   the original string or array with slashes stripped
 */
function removeSlashes ($remove)
{
    // if the argument is an array
    if (is_array($remove))
    {
        // loop through the values
        foreach ($remove as $key => $val)
        {
            // remove the slashes from the value
            $return[$key] = removeSlashes($val);
        }
    }
    // otherwise, remove the slashes from the value
    else
    {
        $return = stripslashes($remove);
    }

    return $return;
}
// removeSlashes function


/**
 * compares two version numbers
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     string  installed   version number of the installed product
 *     string  available   version number of the available product
 * @return integer result
 *     -1      installed version is less than the the available version
 *     0       the versions are equal
 *     1       installed version is greater than the available version
 */
function versionCompare ($installed, $available)
{
    // if either string is empty, return false
    if ($installed == '' || $available == '')
    {
        return false;
    }

    // break up the version parts
    $installed = explode('.', $installed);
    $available = explode('.', $available);

    // find which version has the fewest parts
    $cntI  = count($installed);
    $cntA  = count($available);
    $parts = min($cntI, $cntA);

    // loop through the parts
    for ($cnt = 0; $cnt < $parts; $cnt++)
    {
        // if the installed version is less than the available version
        if ($installed[$cnt] < $available[$cnt])
        {
            return -1;
        }
        // if the installed version is greater than the available version
        else if ($installed[$cnt] > $available[$cnt])
        {
            return 1;
        }
    }

    // if there are more parts in one of the version strings
    if ($cntI != $cntA)
    {
        // find which version has the most parts
        $parts  = max($cntI, $cntA);

        // if there are more parts in the installed version, use the installed
        // version
        if ($cntI == $parts)
        {
            $version = $installed;
        }
        // otherwise, use the available version
        else
        {
            $version = $available;
        }

        // loop through the remaining parts
        for (; $cnt < $parts; $cnt++)
        {
            // if the version part is greater than zero
            if ($version[$cnt] > 0)
            {
                // if there are more parts in the installed version, the 
                // installed version is greater
                if ($cntI == $parts)
                {
                    return 1;
                }
                // otherwise, the available version is greater
                else
                {
                    return -1;
                }
            }
        }
    }

    // if this point has been reached, the versions are equal
    return 0;
}
// versionCompare function

 
/**
 * defines a constant if it is not already defined
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     string  name        the name of the constant to be defined
 *     scalar  value       the scalar value of the constant being defined
 * @return bool
 *     true        constant successfully defined
 *     false       constant already exists
 */
function defineOnce ($name, $value)
{
    //  if the constant is already defined
    if (defined($name))
    {
        return false;
    }
    //  otherwise, define the constant
    else
    {
        define($name, $value);
        return true;
    }
}
// defineOnce function

/**
 * @since      05.12.19
 * @version    05.12.19
 */
function getConstant ($name, $value = false)
{
    // if the constant is set, return the value
    if (defined($name))
    {
        return constant($name);
    }
    // otherwise, return the alternate value
    else
    {
        return $value;
    }
}
// getConstant function


/**
 * outputs a statement if the debug option is set to true
 *
 * @since      05.12.19
 * @version    05.12.19
 *
 * @param
 *     string  statement   printf-style string to parse and output
 *     mixed   args        the arguments to use while parsing
 * @return void
 */
function debug ($statement)
{
    // if the DEBUG constant is defined and set to true
    if (getConstant('DEBUG') && DEBUG)
    {
        if (!is_scalar($statement))
        {
            echo '<pre>';

            // if there is a caption, output it
            if (func_num_args() >= 2)
            {
                echo func_get_arg(1) . "\n";
            }

            // output a preformatted array
            print_r($statement);

            echo '</pre>';
        }
        else
        {
            $args   = func_get_args();

            // if there is at least one argument, delete the first argument
            if (array_key_exists(0, $args))
            {
                unset($args[0]);
            }

            // output the statement
            vprintf($statement, $args);
        }
    }
}
// debug function


function addQuotes($val, $quote = '\'')
{
    return $quote . addslashes($val) . $quote;
}


?>