<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * sums up the amount of time taken between the start() and stop() events
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
 * @since      05.07.01
 * @version    05.12.21
 */
 
 
// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);


class Timer
{
    protected $timerStart; // array of floating-point numbers that represent the start
                     // times for each call of start() that have not yet been 
                     // followed by a call to stop()
    protected $timerCount; // number of times that start() has been called that have 
                     // not yet been followed by a call stop()
    protected $timerTotal; // floating-point number representing the total time that
                     // all timers have been running. If timers were running
                     // simultaneously, only the the outer time is counted


    /**
     * @since      05.07.01
     * @version    05.07.01
     */
    function __construct ()
    {
        $this->timerStart   = array();
        $this->timerCount   = -1;
        $this->timerTotal   = 0;
    }
    // constructor

    
    /**
     * @since      05.12.19
     * @version    05.12.19
     */
    function start ()
    {
        // set a reference to the timerCount variable and increment the
        // number of timers active
        $cnt    = &$this->timerCount;
        $cnt++;

        // record the start time for this timer
        $time                   = explode(' ', microtime());
        $time                   = (float)$time[0] + (float)$time[1];
        $this->timerStart[$cnt] = $time;

        // return the start time
        return $time;
    }
    // start method
    
    /**
     * @since      05.12.19
     * @version    05.12.19
     */
    function stop ()
    {
        // set a reference to the timerCount variable
        $cnt    = &$this->timerCount;

        // if there are not any timers active, return zero duration
        if ($cnt == -1)
            return 0;

        // calculate the end time and the duration the timer was running
        $time       = explode(' ', microtime());
        $time       = (float)$time[0] + (float)$time[1];
        $duration   = $time - $this->timerStart[$cnt];

        // delete the start time
        unset($this->timerStart[$cnt]);

        // decrement the timer count
        $cnt--;

        // if this is the last timer running, add the time to the total
        if ($cnt == -1)
        {
            $this->timerTotal   += $duration;
        }

        // return the duration that the timer was running
        return $duration;
    }
    // stop method

    /**
     * @since      05.12.19
     * @version    05.12.19
     */
    function getTotal()
    {
        return $this->timerTotal;
    }
    // getTotal method
}
//  Timer class


?>
