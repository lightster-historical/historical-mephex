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


class DataCache
{
    protected         $data;

    
    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function __construct ()
    {
        $this->data = array();
    }
    // constructor
    

    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function add ($name, $data)
    {
        #if (!array_key_exists($name, $this->data))
        #{
            $this->data[$name] = $data; 
            return true;
        #}
        #else
        #{
        #    return false;
        #}
    }
    // add method

    /**
     * @since      05.12.28
     * @version    05.12.28
     */         
    function get ($name = null)
    {
        if (is_null($name))
        {
            return $this->data;
        }
        else if (array_key_exists($name, $this->data))
        {
            return $this->data[$name];
        }
        else
        {
            return null;
        }
    }
    // get method
}
// DataCache class


?>
