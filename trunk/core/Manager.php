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
 * @since      05.12.28
 * @version    05.12.28
 */
 
 
// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);

 
/**
 * @dependency com.mephex.core.Function
 */
require_once dirLib . 'com/mephex/core/Function.php';


class Objects
{
    protected         $objs;

    
    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function __construct ()
    {
        $this->objs = array();
    }
    // constructor
    

    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function add ($name, $obj)
    {
        if (!array_key_exists($name, $this->objs))
        {
            $this->objs[$name] = $obj; 
            return true;
        }
        else
        {
            return false;
        }
    }
    // add method

    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function delete ($name)
    {
        if (array_key_exists($name, $this->objs))
        {
            unset($this->objs[$name]);
        }
    }

    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function get ($name)
    {
        if (array_key_exists($name, $this->objs))
        {
            return $this->objs[$name];
        }
        else
        {
            $temp = null;
            return $temp;
        }
    }
    // &get method


    /**
     * @since      05.12.28
     * @version    05.12.28
     */
    function checkDependency ($keyName, $type)
    {
        if (!array_key_exists($keyName, $this->objs))
        {
            trigger_error('<em>' . $keyName . '</em> is required.',
                E_USER_ERROR);
        }
        else if (!($this->objs[$keyName] instanceof $type))
        {
            trigger_error('<em>' . $keyName . '</em> is not of type <em>' . 
                $type . '</em>.', E_USER_ERROR);
        }
        else
        {
            return $this->objs[$keyName];
        }
    }
}
// Objects class


?>
