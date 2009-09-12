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
 * @since      05.12.27
 * @version    05.12.27
 */
 
 
// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);


/**
 * @dependency com.mephex.core.Function
 */
require_once dirLib . 'com/mephex/core/Function.php';
 

class Output
{
    // buffer the output to allow for alternative output (e.g. stats)
    static function bufferBegin ()
    {
        ob_start();
    }



    // end the output buffer session
    static function bufferEnd ($return = false)
    {
        // if the stats are being requested
        if (array_key_exists('stats', $_GET) && $_GET['stats'])
        {
            // store the output and end output buffering
            $contents = ob_get_contents();
            ob_end_clean();

            // if php info is being requested, include the php info stats file
            if ($_GET['stats'] == 'phpinfo')
                phpinfo();
            // otherwise, include the stats file
            else
                require_once($path . 'output.library.stats.php');
        }
        // if the output buffer is supposed to be returned
        else if ($return)
        {
            // store the output and end output buffering
            $contents = ob_get_contents();
            ob_end_clean();

            // return the contents
            return $contents;
        }
        //  otherwise, output and clear the buffer
        else
        {
            // end output buffering and output the buffer contents
            ob_end_flush();
        }
    }



    // output an error
    static function error ($type, $message, $file = false, $line = false)
    {
        // prevent certain types of errors from displaying and
        // decide whether or not to terminate the execution of
        // the program for all error types except the following
        switch ($type)
        {
            #case 0:         // escaped errors (using @)
            #    return;
            case E_WARNING:     // warnings
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
            case E_NOTICE:      // notices
            case E_USER_NOTICE:
            case E_STRICT:      // PHP5 syntax changes
                $exit   = false;
                break;
            default:
                $exit   = true;
                break;
        }

        // end and clear all output buffer sessions
        while (ob_get_level() > 0)
            ob_end_clean();

        $messages   = array('Unknown');
        $types  = array(
                        E_ERROR             => 'Fatal run-time error',
                        E_WARNING           => 'Run-time warning',
                        E_PARSE             => 'Compile-time parse error',
                        E_NOTICE            => 'Run-time notice',
                        E_CORE_ERROR        => 'Fatal core error',
                        E_CORE_WARNING      => 'Core warning',
                        E_COMPILE_ERROR     => 'Fatal compile-time error',
                        E_COMPILE_WARNING   => 'Compile-time warning',
                        E_USER_ERROR        => 'Fatal software error',
                        E_USER_WARNING      => 'Software warning',
                        E_USER_NOTICE       => 'Software notice',
                        E_STRICT            => 'PHP4 software running on PHP5 server'
                       );

        $number     = 0;#intval($message);
        #$message    = array_key_exists($number, $messages) ? $messages[$number] : $messages[0];

        ?>
         <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
         <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
          <head>
           <title>Error</title>
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
           <style type="text/css">
            body, .body, tr
            {
             color:            #000000;
             text-decoration:  none;
             font-family:      arial, sans-serif;
             font-size:        small;
            }

            #container
            {
              margin:          0px auto;
              padding:         0px;

              width:           600px;
            }

            h1, h2, dt
            {
              margin:          0px;
              padding:         0px;

              font-family:     arial, sans-serif;
              font-size:       large;
              font-style:      italic;
              font-weight:     bold;
            }

            h2
            {
              margin:          0px;
              padding:         0px;

              font-size:       medium;
            }

            #details dt
            {
              margin:          0px 0px;
              padding:         2px;

              font-size:       small;
              font-style:      normal;

              float:           left;
              clear:           left;
            }

            #details dd
            {
              margin:          0px 0px 0px 75px;
              padding:         2px;
            }

            #support dt
            {
              margin:          0px 0px;
              padding:         2px;

              font-size:       small;
              font-style:      normal;
            }

            #support dd
            {
              margin:          0px 0px 0px 25px;
              padding:         2px;
            }

            a
            {
              color:           #0000cc;
            }
           </style>
          </head>
          <body>
           <div id="container">
            <h1>Error</h1>
            <p>An error occurred during the processing of the page request.</p>
            <h2>Details</h2>
            <p>
             <dl id="details">
              <dt>Type</dt>
              <dd><?php echo $types[$type]; ?> (<?php echo $type; ?>)</dd>
              <dt>Number</dt>
              <dd><?php echo $number; ?></dd>
              <dt>Message</dt>
              <dd><?php echo $message; ?></dd>
              <dt>File</dt>
              <dd><?php echo $file; ?></dd>
              <dt>Line</dt>
              <dd><?php echo $line; ?></dd>
             </dl>
            </p>
        <?php

        if (function_exists('debug_backtrace'))
        {
            $backtrace  = debug_backtrace();
            if (isset($backtrace[0]['args']))
            {
                unset($backtrace[0]['args'][4]);
            }
            ?>
             <h2>Program Backtrace</h2>
            <?php

            foreach ($backtrace as $curr)
            {
                echo '<pre style="overflow: auto; ">';
                print_r($curr);
                echo '</pre>';
            }

            /*
            $backtrace  = serialize($backtrace);
            $backtrace  = base64_encode($backtrace);
            echo $backtrace . '<pre>';
            print_r(unserialize(base64_decode($backtrace)));
            */
        }

        /*
        ?>
            <h2>Support</h2>
            <p>
             <dl id="support">
              <dt><a href="" target="_blank">Troubleshoot</a></dt>
              <dd>If you are unable to resolve your problem on your own, the troubleshooter has information on resolving common problems.</dd>
              <dt><a href="" target="_blank">Further Support</a></dt>
              <dd></dd>
             </dl>
            </p>
        <?php
        */

        ?>
           </div>
          </body>
         </html>
        <?php

        // if the program should be terminated, terminate the script
        if ($exit)
            exit;
    }

}
// Output class


?>
