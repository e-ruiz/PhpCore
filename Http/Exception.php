<?php
/**
 * Http Request class file
 *
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 16:10:32 BRST 
 * @license http://opensource.org/licenses/MIT
 */
 
 
namespace Core\Http;


/**
 * Http Exception
 *
 * @package Core\Http
 * @author Eric S. Lucinger Ruiz <eu@ericruiz.com.br>
 * @version Sun 12 Jan 2014 16:10:32 BRST 
 * @license http://opensource.org/licenses/MIT
 */
class Exception 
    extends \Exception
{
    /**
     * @var array
     * @see http://www.w3schools.com/tags/ref_httpmessages.asp (last access: 25/nov/2014)
     */
    public static $status = array(
        
        // 1xx Information
        100 => 'Continue',
        101 => 'Switching Protocols',
        103 => 'Checkpoint',
        
        // 2xx Successful
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        
        // 3xx Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Resume Incomplete',
        
        // 4xx Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        
        // 5xx Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        511 => 'Network Authentication Required',
    );
    
    /**
     * 
     * @param mixed $message can be a status code or message
     * @param integer $code
     * @param integer $previous
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        // if message is a valid status code, I will map it (;
        if (array_key_exists($message, self::$status) && $code == 0)
            return parent::__construct(self::$status[$message], $message, $previous);
        
        // have message with default code, concatenate message
        if ($message && $code > 0 && array_key_exists($code, self::$status))
            return parent::__construct(self::$status[$code] . ': ' . $message, $code, $previous);
        
        // default Exception behavior
        return parent::__construct($message, $code, $previous);
    }
}
