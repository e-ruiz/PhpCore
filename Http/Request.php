<?php
/**
 * Http Request class file
 *
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 16:10:32 BRST 
 * @license http://opensource.org/licenses/MIT
 */
 
 
namespace Core\Http;


use Core\Http\Exception;


/**
 * Http Request
 *
 * An abstraction of incoming request 
 *
 * @todo parse and support multipart/form-data
 *
 * @package Core\Http
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 */
class Request
{
	/**
	 * List of a valid methods
	 * 
	 * It would be a CONSTANT, but it forces to run on PHP 5.6
	 * 
	 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9
	 * @var array
	 */
	public static  $methods = array(
			'GET','POST','PUT','DELETE',
			'OPTIONS','CONNECT','TRACE','HEAD');
	
	/**
	 * @var string
	 */
	private $_method;
	
	/**
	 * @var \Core\Http\Url
	 */
	private $_url;
	
	/**
	 * @var string
	 */
	private $_body;
	
	/**
	 * @var array
	 */
	private $_headers;
	
	/**
	 * @var array
	 */
	private $_cookies;
	
	public function __construct()
	{
		$this->_method  = $this->getMethod();
		$this->_url     = $this->getUrl();
		$this->_body    = $this->getBody();
		$this->_headers = $this->getHeaders();
		$this->_cookies = $this->getCookies();
	}
	
	/**
	 * @todo refactor check if override is valid else raise exception
	 */
	public static function getMethod()
	{
		$override = (self::getHeaders('X-Http-Method-Override'))
				  ? strtoupper(self::getHeaders('X-Http-Method-Override'))
				  : null;
	
		$override = (in_array($override, self::$methods))
				  ? $override
				  : null;
	
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
	
		return ($override) ? $override : $method;
	}

	public static function getUrl()
	{
		return new \Core\Http\Url();
	}
		
	public static function getBody()
	{
		$contentType = self::getHeaders('Content-Type');
		$contentType = explode(';', preg_replace('~( )~', '', $contentType));

		/*
		 * @todo parse multpart form data, specially when method is PUT
		 */
		if (in_array('multipart/form-data', $contentType))
			throw new \Core\Http\Exception(415);

		parse_str(file_get_contents("php://input"), $body);
		
		return $body;
	}
	
	/**
	 * @todo check RFC about case-insensitive headers names
	 *
	 * @param string $param
	 * @return mixed
	 */
	public static function getHeaders($param = null)
	{
		$headers = array();
		foreach ($_SERVER as $key => $value) {
			if (preg_match('~^(http)~i', $key)) {
	
				// HTTP_REQUEST_HEADER becomes Request-Header
				$key = preg_replace('~^(http_)~i', '', $key);
				$key = preg_replace('~(_)~', ' ', $key);
				$key = ucwords(strtolower($key)) . '-';
				$key = preg_replace('~( )~', '-', $key);
				$key = trim($key, '-');
	
				$headers[$key] = $value;
			}
		}

		if ($param) {
			if (isset($headers[$param]))
				return $headers[$param];
			else
				return null;
		}
		
		return $headers;
	}
	
	public static function getCookies($param = null)
	{
		if ($param) {
			if (isset($_COOKIE[$param]))
				return $_COOKIE[$param];
			else
				return null;
		}
	
		return $_COOKIE;
	}
	
	/**
	 * Check if $_SERVER['HTTP_X_REQUESTED_WITH'] === 'xmlhttprequest'
	 */
	public static function isAjax()
	{
		$ajax = strtolower(\Core\Http\Request::getHeaders('X-Requested-With'));
		return ($ajax === 'xmlhttprequest');
	}
}
