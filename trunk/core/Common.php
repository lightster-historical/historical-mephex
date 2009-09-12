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
 * @link       05.12.18
 * @since      05.12.27
 */
 
 
error_reporting(E_ALL | E_STRICT);


// this software should not be running if the paths are not set
if (!defined('dirLib')) trigger_error('dirLib is not set', E_USER_ERROR);
if (!defined('dirCfg')) trigger_error('dirCfg is not set', E_USER_ERROR);


/**
 * @dependency com.mephex.core.Function
 * @dependency com.mephex.core.Cookie
 * @dependency com.mephex.core.Date
 * @dependency com.mephex.core.Input
 * @dependency com.mephex.core.Objects
 * @dependency com.mephex.core.Output
 * @dependency com.mephex.core.Timer
 * @dependency com.mephex.db.Db
 * @dependency com.mephex.setting.Setting
 * @dependency com.mephex.theme.Theme
 * @dependency com.mephex.user.User
 * @dependency com.mephex.user.PrmFramework
 */
require_once dirLib . 'com/mephex/core/Function.php';

// include the configuration file
require_once getConstant('CONFIG', dirCfg . 'core.php');

require_once dirLib . 'com/mephex/core/Cookie.php';
require_once dirLib . 'com/mephex/core/Date.php';
require_once dirLib . 'com/mephex/core/Input.php';
require_once dirLib . 'com/mephex/core/Objects.php';
require_once dirLib . 'com/mephex/core/Output.php';
require_once dirLib . 'com/mephex/core/Timer.php';
require_once dirLib . 'com/mephex/db/Db.php';
require_once dirLib . 'com/mephex/setting/Setting.php';
require_once dirLib . 'com/mephex/theme/Theme.php';
require_once dirLib . 'com/mephex/user/User.php';
require_once dirLib . 'com/mephex/user/PrmFramework.php';


// create and start a timer
$timer = new Timer();
$timer->start();


// tell PHP to let the script handle errors
#set_error_handler(array('Output', 'error'));


$objs = new Objects();


// create a connection to the database server 
$db = &db(DB_HOSTTYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD,
    DB_DATABASE, DB_HOSTPORT, DB_TBPREFIX, DB_TBPSTFIX);
$objs->add('db', $db);

// retrieve the stored settings
$setting = new Setting($db);
$objs->add('setting', $setting);


//
$cookie = new Cookie($setting->get('cookie_prefix'),
    $setting->get('cookie_pstfix'), 0, $setting->get('cookie_path'),
    $setting->get('cookie_domain'), $setting->get('cookie_secure'));
$objs->add('cookie', $cookie);

//
$input = new Input();
$objs->add('input', $input);

$prmFramework = new PrmFramework();
$objs->add('prmFramework', $prmFramework);


defineOnce('PAGE_ADMIN', false);

if (!isset($components) || !is_array($components))
{
    $components = array();
}
$components[] = array('core', 'com/mephex/core');
$components[] = array('user', 'com/mephex/user');

foreach ($components as $component)
{
    if (file_exists(dirCfg . '/' . $component[0] . '.php'))
    {
        include_once dirCfg . '/' . $component[0] . '.php';
    }
    if (file_exists(dirLib . $component[1] . '/Init.php'))
    {
        include_once dirLib . $component[1] . '/Init.php';
    }
}


User::signIn($objs, 'demo', 'demo');

// get the 
$user  = &User::getSession($objs);
$objs->add('user', $user);

if (!getConstant('PAGE_ADMIN', false))
{
    $theme = new Theme($objs, $user->getProperty('themeId'));
    $objs->add('theme', $theme);
}

#$lang  = new Language($common, $user->getProperty('langId'));


// buffer the output
Output::bufferBegin();


?>