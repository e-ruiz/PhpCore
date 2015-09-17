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
	/**
	 * @var string
	 */
	private $_scheme = '';
	
	/**
	 * @var string
	 */
	private $_user = '';
	
	/**
	 * @var string
	 */
	private $_pass = '';
	
	/**
	 * @var string
	 */
	private $_host = '';
	
	/**
	 * @var string
	 */
	private $_port = '';
	
	/**
	 * @var string
	 */
	private $_path = '';
	
	/**
	 * @var string
	 */
	private $_query = '';
	
	/**
	 * @var string
	 */
	private $_fragment = '';
	
	public function __construct($url = null)
	{
		if ($url == null) {
		
			$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			     ? 'https://' : 'http://';
			
			$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		
		if (!$parsed = parse_url($url))
			throw new Exception('Malformed URL!');
		
		$this->_scheme = parse_url($url, PHP_URL_SCHEME);
		$this->_user = parse_url($url, PHP_URL_USER);
		$this->_pass = parse_url($url, PHP_URL_PASS);
		$this->_host = parse_url($url, PHP_URL_HOST);
		$this->_port = parse_url($url, PHP_URL_PORT);
		$this->_path = parse_url($url, PHP_URL_PATH);
		$this->_query = parse_url($url, PHP_URL_QUERY);
		$this->_fragment = parse_url($url, PHP_URL_FRAGMENT);
	}
	
	/**
	 * "Unparse" the URL
	 * 
	 * @return string
	 */
	public function __toString()
	{
		/*
		 * @author thomas at gielfeldt dot com
		 * @link http://php.net/manual/en/function.parse-url.php
		 */
		$scheme   = isset($this->_scheme)   ? $this->_scheme . '://' : '';
		$host     = isset($this->_host)     ? $this->_host           : '';
		$port     = isset($this->_port)     ? ':' . $this->_port     : '';
		$user     = isset($this->_user)     ? $this->_user           : '';
		$pass     = isset($this->_pass)     ? ':' . $this->_pass     : '';
		$pass     = ($user || $pass)        ? "$pass@"               : '';
		$path     = isset($this->_path)     ? $this->_path           : '';
		$query    = isset($this->_query)    ? '?' . $this->_query    : '';
		$fragment = isset($this->_fragment) ? '#' . $this->_fragment : '';
		
		return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
	}
	
	/**
	 * @return string|null
	 */
	public function getScheme()
	{
		return $this->_scheme;
	}
	
	/**
	 * @return string|null
	 */
	public function getUser()
	{
		return $this->_user;
	}
	
	/**
	 * @return string|null
	 */
	public function getPass()
	{
		return $this->_pass;
	}
	
	/**
	 * @return string|null
	 */
	public function getHost()
	{
		return $this->_host;
	}
	
	/**
	 * @return string|null
	 */
	public function getPort()
	{
		return $this->_port;
	}
	
	/**
	 * @return string|null
	 */
	public function getPath()
	{
		return $this->_path;
	}
	
	/**
	 * Return query string as an associative array
	 * 
	 * @param string $param param name
	 * @return array|null
	 */
	public function getQuery($param = null)
	{
		$query = array();
		foreach (explode('&', $this->_query) as $arg) {
			$z = explode('=', $arg);
			$query[$z[0]] = $z[1];
		}
		
		// remember 0 (zero) is a valid index
		if ($param === null)
			return $query;
		
		if (array_key_exists($param, $query))
			return $query[$param];
		else
			return null;
	}
	
	/**
	 * @return string|null
	 */
	public function getFragment()
	{
		return $this->_fragment;
	}
}
