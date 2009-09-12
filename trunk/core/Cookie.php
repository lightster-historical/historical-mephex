<?php



require_once PATH_LIB . 'com/mephex/core/Function.php';



class Cookie
{
    private static $instance = null;
    
    protected $namePrefix; // the prefix that will be placed before each cookie name
    protected $namePstfix; // the postfix that will be placed behind each cookie name
    protected $expire;     // the number of minutes before each cookie expires
    protected $path;       // the path portion of the url that the script is located
    protected $domain;     // the domain the cookie is being set under
    protected $secure;     // whether or not the cookie should be sent only under a 
                     //     secure connection

            
    /**
     * @since      05.12.19
     * @version    05.12.22
     */         
    private function __construct ($prefix = '', $suffix = '', $expire = 0, $path = '', 
        $domain = '', $secure = false)
    {
        $this->namePrefix = $prefix;
        $this->nameSuffix = $suffix;
        $this->expire     = ($expire == 0 ? 0 : ($expire * 60 + time()));
        $this->path       = $path;
        $this->domain     = $domain;
        $this->secure     = $secure;
    }
    // constructor

    
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    function get ($name)
    {
        // set the cookie name
        $name   = $this->namePrefix . $name . $this->nameSuffix;

        // if the cookie exists, return the value of it
        if (array_key_exists($name, $_COOKIE))
        {
            if (get_magic_quotes_gpc())
            {
                return removeSlashes($_COOKIE[$name]);
            }
            else
            {
                return $_COOKIE[$name];
            }
        }
        // otherwise, return unknown
        else
        {
            return null;
        }
    }
    // get method
    
    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    function set ($name, $value, $expire = null)
    {
        // set the cookie name
        $name   = $this->namePrefix . $name . $this->nameSuffix;

        // if the expiration is boolean false, set the expiration
        // so the cookie expires when the browser is closed
        if ($expire === true)
        {
            $expire = 0;
        }
        // if the expiration is null, use the expiration provided by the object
        else if (is_null($expire))
        {
            $expire = $this->expire;
        }
        // otherwise, expire in the number of minutes provided
        else
        {
            $expire = time() + intval($expire * 60);
        }

        // set the cookie
        setcookie($name, $value, $expire, $this->path, $this->domain, 
            $this->secure);

        return true;
    }
    // set method

    /**
     * @since      05.12.19
     * @version    05.12.22
     */
    function delete ($name = null)
    {
        // if a cookie name was not provided
        if (is_null($name))
        {
            // delete each cookie that has the correct name pre-/post-fix
            foreach (array_keys($_COOKIE) as $name)
            {
                $prefix = '';
                $suffix = '';

                // if a prefix is set
                if (strlen($this->namePrefix) > 0)
                {
                    $prefix = substr($name, 0, strlen($this->namePrefix));
                }

                // if a postfix is set
                if (strlen($this->nameSuffix) > 0)
                {
                    $suffix = substr($name, -strlen($this->nameSuffix));
                }

                // if the prefix and postfix match, delete the cookie
                if ($prefix == $this->namePrefix && $suffix == $this->nameSuffix)
                {
                    setcookie($name, false, time()-3600, $this->path,
                        $this->domain, $this->secure);
                }
            }
        }
        else
        {
            // set the cookie name
            $name   = $this->namePrefix . $name . $this->nameSuffix;

            // delete the cookie
            setcookie($name, false, time()-3600, $this->path, $this->domain,
                $this->secure);
        }
    }
    // delete method
    
    
    public static function getInstance($prefix = '', $suffix = '', $expire = 0, $path = '', 
        $domain = '', $secure = false)
    {
        if(is_null(self::$instance))
        {
            self::$instance = new Cookie($prefix, $suffix, $expire, $path
                , $domain, $secure);
        }
        
        return self::$instance;
    }
}
// Cookie class


?>
