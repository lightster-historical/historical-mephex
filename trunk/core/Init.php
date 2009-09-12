<?php
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
 * @version
 * @link       05.12.27
 * @since      05.12.27
 */


// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);


/**
 * @dependency com.mephex.core.Function
 */
require_once dirLib . 'com/mephex/core/Function.php';


// define the permission framework
$prmFramework->defineSet('core');
$prmFramework->define('core', 'admin');


if (getConstant('PAGE_ADMIN', false))
{
}


?>