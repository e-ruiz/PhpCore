<?php
/**
 * Http Url class file
 *
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 16:10:32 BRST 
 * @license http://opensource.org/licenses/MIT
 */


namespace Core\Http;

/**
 * Http Url
 *
 * @package Core\Http
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 16:10:32 BRST 
 */
class Url
{
    private $_url = '';
    
    public $parsed = array (
            'scheme' => null,
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'path' => null,
            'query' => null,
            'fragment' => null
    );
    
    /**
     * Setup a URL string, map and parse that
     *
     * @todo need more studies arround security and server (Apache, Nginx) enviroments
     *
     * @param string $url if is null, it will get current URL by $_SERVER enviroment
     */
    public function __construct($url = null)
    {
        if ($url === null) { 
        
            $this->_url  = (Request::server('HTTPS') == 'on') ? 'https://' : 'http://';
            $this->_url .= Request::server('HTTP_HOST') . Request::server('REQUEST_URI');
        
        } else {
        
            $this->_url = $url;
        }
        
        $this->_mapParsedUrl(parse_url($this->_url));
        
        if (!$this->parsed)
            throw new Exception('Malformed URL!');
    }
    
    /**
     * Echoes a parsed URL
     *
     * @return string
     */
    public function __toString()
    {
        return $this->unparseUrl($this->parsed);
    }
    
    /**
     * If is trying to get some parsed param, return this param
     *
     * @param string $param
     * @return mixed
     */
    public function __get($param)
    {
        if (array_key_exists($param, $this->parsed))
            return $this->parsed[$param];
    }
    
    /**
     * If is trying to set some URL structure param, maps that on $_parsed
     *
     * @param string $param
     * @return mixed
     */
    public function __set($param, $value)
    {
        if (array_key_exists($param, $this->parsed))
            $this->parsed[$param] = $value;
            
        else
            $this->$param = $value;
    }
    
    /**
     * Map parse_url keys into $this->parsed[]
     *
     * @param array $parsed_url
     * @return void
     */
    private function _mapParsedUrl(array $parsed_url)
    {
        foreach ($parsed_url as $key => $value)
            $this->parsed[$key] = $value;
    }
    
    /**
     * Unparse an array provided by parse_url()
     *
     * @author thomas at gielfeldt dot com (from php.net)
     * @see http://php.net/manual/en/function.parse-url.php#106731
     * @param array $parsed_url
     * @return string
     */
    private function unparseUrl(array $parsed_url = null)
    {
        // some implementation
        $parsed_url = ($parsed_url == null) ? $this->parsed : $parsed_url;
        if (!$parsed_url)
            throw new InvalidArgumentException('Invalid argument!', 500);
            
        // original code
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        
        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    } 
}