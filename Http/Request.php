<?php
/**
 * Http Request class file
 *
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 14:27:40 BRST 
 * @license http://opensource.org/licenses/MIT
 */

namespace Core\Http;

/**
 * Http Request
 *
 * @package Core\Http
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 14:27:40 BRST 
 */
class Request
{
    /**
     * @var self instance
     */
    private static $_instance;
    
    /**
     * @var array valid http methods list
     */
    public static $validMethods = array(
            'GET','POST','PUT','PATCH','DELETE','HEAD','OPTIONS', 'TRACE', 'CONNECT');
    
    /**
     * @var Http\RequestUrl URL object
     */
    public $url;
    
    /**
     * @var string Http Request body 
     */
    public $body;
    
    /**
     * @var array cookies list
     */
    public $cookies;
    
    /**
     * Singleton constructor
     *
     * @access private
     */
    private function __construct()
    {
        $this->method = $this->getMethod();
        
        // setup url
        $this->url = new Url();
        
        // map get
        $this->_mapHeaders();
        
        // map body, (!) alpha version 
        $this->_mapBody();
        
        // map cookies
        $this->cookies = $this->cookie();
    }
    
    /**
     * Singleton pattern, so you can not clone it
     *
     */
    private function __clone() {}
    
    /**
     * Singleton pattern 
     *
     * @return \Core\Http\Request
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self)
            self::$_instance = new self();
        
        return self::$_instance;
    }
    
    /**
     * $_SERVER alias
     *
     * @param string $var
     * @return mixed
     */
    public static function server($var = null)
    {
        if ($var !== null)
            return (isset($_SERVER[$var]))
                ? $_SERVER[$var]
                : null;
        
        return $_SERVER;
    }
    
    /**
     * $_REQUEST alias
     *
     * @param string $var
     * @return mixed
     */
    public static function request($var = null)
    {
        if ($var !== null)
            return (isset($_REQUEST[$var]))
                ? $_REQUEST[$var]
                : null;
        
        return $_REQUEST;
    }
    
    /**
     * $_GET alias
     *
     * @param string $var
     * @return mixed
     */
    public static function get($var = null)
    {
        if ($var !== null)
            return (isset($_GET[$var]))
                ? $_GET[$var]
                : null;
        
        return $_GET;
    }
    
    /**
     * $_POST alias
     *
     * @param string $var
     * @return mixed
     */
    public static function post($var = null)
    {
        if ($var !== null)
            return (isset($_POST[$var]))
                ? $_POST[$var]
                : null;
        
        return $_POST;
    }
    
    /**
     * $_COOKIE alias
     *
     * @param string $var
     * @return mixed
     */
    public function cookie($var = null)
    {
        if ($var !== null)
            return (isset($_COOKIE[$var]))
                ? $_COOKIE[$var]
                : null;
        
        return $_COOKIE;
    }
    
    /**
     * Returns Http requested method
     *
     * Supports X-Http-Method-Override header and POST overhide under _METHOD field
     *
     * @todo refactor, check good practices
     *
     * @return string
     */
    public static function getMethod()
    {
        $method = strtoupper(self::server('REQUEST_METHOD'));
        
        // overrides all, return if method is valid, else returns null
        if ($override = strtoupper(self::server('HTTP_X_HTTP_METHOD_OVERRIDE')))
            return (in_array($override, self::$validMethods))
                ? $override
                : 'UNKNOW_METHOD: ' . $override;
        
        /*
         * POST form override method --> POST x-www-form-urlencoded field:_METHOD=PUT
         * Useful with browsers who do not support some http methods like PUT
         */
        if (strtoupper(self::server('REQUEST_METHOD')) == 'POST')
            // overrides http method, return if method is valid, else returns null
            if ($override = strtoupper(self::post('_METHOD')))
                return (in_array($override, self::$validMethods))
                    ? $override
                    : 'UNKNOW_METHOD: ' . $override;
        
        // default http method discoverer
        return (in_array($method, self::$validMethods))
            ? $method
            : 'UNKNOW_METHOD: ' . $method; // just in case;
    }
    
    /**
     * Maps http request body
     *
     * @todo REFACTOR! Alpha version!
     *
     * @see http://php.net/manual/en/function.http-get-request-body.php
     * @see http://php.net/manual/en/reserved.variables.httprawpostdata.php
     * @return void
     */
    private function _mapBody()
    {
        if ($this->method == 'GET' || $this->method == 'POST') {
            $this->body = $this->request();
            
            // $this->body_raw = ??;
            return;
        }
        
        $this->body = null;
        if ($body_raw = @file_get_contents('php://input')) {  
        
            foreach (explode('&', $body_raw) as $value) {
            
                $val = substr($value, strpos($value, '=') + 1);
                $key = substr($value, 0, strpos($value, '='));
                
                $this->body[$key] = $val;
            }
        }
    }
    
    /**
     * Map request headers
     *
     * @return void
     */
    private function _mapHeaders()
    {
        foreach ($this->server() as $key => $value) {
        
            if (preg_match('~^(http)~i', $key)) {
                
                $key = preg_replace('~^(http_)~i', '', $key);
                
                $key = preg_replace('~(_)~', ' ', $key);
                $key = ucwords(strtolower($key)) . '-';
                $key = preg_replace('~( )~', '-', $key);
                
                $key = trim($key, '-');
                $this->headers[$key] = $value;
            }
        }
    }
    
    /**
     * Map $_POST
     *
     * @todo refactor
     * @return void
     */
    private function _mapPost()
    {
        foreach ($this->post() as $field => $value) {
            
            $this->post[$field] = $value;
        }
    }
    
    /**
     * Map $_GET
     *
     * @todo refactor
     * @return void
     */
    private function _mapGet()
    {
        foreach ($this->get() as $field => $value) {
            
            $this->get[$field] = $value;
        }
    }
    
    /**
     * Returns client IP even behind proxies
     *
     * @todo REFACTOR! test with different servers than apache
     */
    public function getClientIp()
    {
        foreach (array('HTTP_CLIENT_IP',
                        'HTTP_X_FORWARDED_FOR',
                        'HTTP_X_FORWARDED',
                        'HTTP_X_CLUSTER_CLIENT_IP',
                        'HTTP_FORWARDED_FOR',
                        'HTTP_FORWARDED',
                        'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $IPaddress){
                    $IPaddress = trim($IPaddress); // Just in case

                    /*
                    if (filter_var($IPaddress,
                                   FILTER_VALIDATE_IP,
                                   FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                        !== false) {
                    */

                        return $IPaddress;
                    //}
                }
            }
        }
    }
}
