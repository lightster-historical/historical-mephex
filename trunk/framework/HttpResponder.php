<?php


/**
 * @import com.mephex.core.HttpHeader
 * @import com.mephex.framework.HttpError
 */
require_once PATH_LIB . 'com/mephex/core/Exception.php';
require_once PATH_LIB . 'com/mephex/core/HttpHeader.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/UndefinedClassException.php';
require_once PATH_LIB . 'com/mephex/core/UndefinedMethodException.php';
require_once PATH_LIB . 'com/mephex/dev/Debug.php';
require_once PATH_LIB . 'com/mephex/framework/HttpError.php';


class HttpResponder
{
    protected $input;
    protected $args;


    public function init($args)
    {
        $this->args = $args;
        $this->input = new Input($args);
    }

    public function get($args)
    {
        $this->request($args);
    }

    public function post($args)
    {
        $this->request($args);
    }

    public function request($args)
    {
        HttpHeader::sendErrorStatus(405, HttpError::getInstance($args));
    }


    protected function checkPermissions()
    {
    }


    public function printHeader()
    {
    }

    public function printFooter()
    {
    }


    public function getInput()
    {
        return $this->input;
    }


    public function getDisplayErrorTypes()
    {
        return array('all');
    }


    public function outputException(Exception $ex)
    {
        echo '<div class="mephex-exception">';
        echo '<h3>Exception caught by Mephex Framework</h3>';
        echo '<pre class="mephex-message">';
        print_r($ex->__toString());
        echo '</pre>';
        echo '<pre class="mephex-trace">';
        print_r($ex->getTraceAsString());
        echo '</pre>';
        echo '</div>';
    }



    public static function run($className)
    {
        if(count($_POST) > 0)
        {
            $methodName = 'post';
            $vars = &$_POST;
        }
        else
        {
            $methodName = 'get';
            $vars = &$_GET;
        }
        $vars = &$_REQUEST;

        if(defined('PATH_CONFIG'))
        {
            include_once PATH_CONFIG;
        }

        if(class_exists($className) || class_exists('PageResponder'))
        {
            if(!class_exists($className))
                $className = 'PageResponder';

            $obj = new $className($vars);

            if($obj instanceof HttpResponder)
            {
                error_reporting(E_ALL | E_STRICT);
                $lastErrorHandler = set_error_handler(array($obj, 'translateErrorToException'));

                try
                {
                    call_user_func(array($obj, 'init'), $vars);
                    call_user_func(array($obj, 'checkPermissions'), $vars);
                    call_user_func(array($obj, $methodName), $vars);

                    $content = ob_get_contents();
                    ob_end_clean();
                    echo trim($content);
                }
                catch(Exception $ex)
                {
                    if(!($ex instanceof MXT_Exception)
                        || in_array('all', $obj->getDisplayErrorTypes())
                        || in_array($ex->getType(), $obj->getDisplayErrorTypes()))
                    {
                        MXT_Debug::logException('mephex', $ex);
                        //$obj->outputException($ex);
                    }
                }

                if(!is_null($lastErrorHandler))
                {
                    set_error_handler($lastErrorHandler);
                }
                else
                {
                    restore_error_handler($lastErrorHandler);
                }
            }
            else
            {
                HttpHeader::sendErrorStatus(405, HttpError::getInstance($vars));
            }
        }
        else
        {
            HttpHeader::sendErrorStatus(500, HttpError::getInstance($vars));
        }
    }


    public function translateErrorToException($errNum, $errStr, $errFile, $errLine)
    {
        throw new ErrorException($errStr, 0, $errNum, $errFile, $errLine);
    }
}


?>
