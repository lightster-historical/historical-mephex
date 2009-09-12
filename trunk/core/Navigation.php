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
 * @since      06.01.08
 * @version    06.01.08
 */
 
 
// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);


class Navigation
{
    protected $index;
    protected $title;
    protected $url;
    protected $target;
    
    protected $subItems;
    
    protected $sort;
    

    function __construct ($sort = false)
    {
        $this->index    = null;
        $this->title    = null;
        $this->url      = null;
        $this->target   = null;
        
        $this->sort     = $sort;
        
        $this->subItems = array();
    }
    // constructor
    
    
    function add ($index, $title, $url = null, $target = null)
    {
        preg_match('/(\w+)/', $index, $match);
        $index = $match[1];
        
        if (!array_key_exists($index, $this->subItems))
        {
            $this->subItems[$index] = new Navigation();
            $this->subItems[$index]->index = $index;
            $this->subItems[$index]->setTitle($title);
            $this->subItems[$index]->setURL($url);
            $this->subItems[$index]->setTarget($target);
        }
        else
        {
            trigger_error("A navigation item with index '$index' already " . 
                "exists.", E_USER_ERROR);
        }
    }
    // add method
    
    function get ($index = null, $subItems = false)
    {        
        if (!is_null($index))
        {
            preg_match('/(\w+)/', $index, $match);
            $index = $match[1];
            
            if (array_key_exists($index, $this->subItems))
            {
                if ($subItems)
                {
                    return $this->subItems[$index]->get();
                }
                else
                {
                    return $this->subItems[$index];
                }
            }
        }
        else
        {
            if ($this->sort)
            {
                ksort($this->subItems);
            }
            
            return $this->subItems;
        }
        
    }
    // get method
    
    function exists ($index)
    {
        preg_match('/(\w+)/', $index, $match);
        $index = $match[1];
        
        if (array_key_exists($index, $this->subItems))
        {
            return true;
        }
        else
        {
            return false;
        }        
    }
    // exists method
    
    
    function setTitle ($title)      {$this->title  = $title;}
    function setURL ($url)          {$this->url    = $url;}
    function setTarget ($target)    {$this->target = $target;}
    
    function getIndex ()            {return $this->index;}
    function getTitle ()            {return $this->title;}
    function getURL ()              {return $this->url;}
    function getTarget ()           {return $this->target;}
}
// Navigation class


?>
