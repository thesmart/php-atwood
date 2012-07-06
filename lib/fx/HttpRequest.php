<?php
namespace Atwood\lib\fx;

use \Atwood\lib\Url;

/**
 * Encapsulates the request from the web-client
 */
class HttpRequest {

	const METHOD_GET	= 'get';
	const METHOD_POST	= 'post';
	const METHOD_PUT	= 'put';
	const METHOD_DELETE	= 'delete';

	/**
	 * HttpRequest::METHOD_*
	 * @var string
	 */
	public $method;

	/**
	 * @var null|string
	 */
	public $ip;

	/**
	 * @var \Atwood\lib\Url
	 */
	public $url;

	/**
	 * Is this request running over SSL?
	 * @var bool
	 */
	public $isHttps;

	/**
	 * @var array
	 */
	public $get;

	/**
	 * @var array
	 */
	public $post;

	/**
	 * @var array
	 */
	public $put;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * @var array
	 */
	public $headers;

	/**
	 * Constructor
	 * @param array $server			Optional. Defaulted to $_SERVER
	 */
	public function __construct(array $server) {
		$server = isset($server) ? $server : $_SERVER;

		$this->get		= $_GET;
		$this->post		= $_POST;
		$this->put		= array();

		if ($this->method === HttpRequest::METHOD_PUT) {
			if (function_exists('mb_parse_str')) {
				mb_parse_str(file_get_contents('php://input'), $this->put);
			} else {
				parse_str(file_get_contents('php://input'), $this->put);
			}
			$this->data		= $this->put + $_GET;
		} else if ($this->method === HttpRequest::METHOD_POST) {
			$this->data		= $_POST + $_GET;
		} else {
			$this->data		= $_GET;
		}

		$this->method = isset($server['REQUEST_METHOD']) ? strtolower($server['REQUEST_METHOD']) : 'get';
		$this->ip		= self::GetIp($server);
		$this->url		= new Url($server['REQUEST_URI']);
		$this->isHttps	= !empty($server['HTTPS']);
		$this->headers	= self::GetHeaders($server);
	}

	/**
	 * Get the user's IP Address
	 *
	 * @static
	 * @param array $server		Optional. Defaulted to $_SERVER
	 * @return null|string		Client's Ip-address, or null if not found
	 */
	private static function GetIp(array $server = null) {
		$server = isset($server) ? $server : $_SERVER;

		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (!array_key_exists($key, $server)) {
				continue;
			}

			foreach (explode(',', $server[$key]) as $ip){
				$ip = trim($ip); // just to be safe

				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
					return $ip;
				}
			}
		}

		return null;
    }

	/**
	 * Get all the custom HTTP_* headers
	 * @static
	 * @param array $server		Optional. Defaulted to $_SERVER
	 * @return array
	 */
	private static function GetHeaders(array $server = null) {
		$server		= isset($server) ? $server : $_SERVER;
		$headers	= array();

		foreach($_SERVER as $key => $val) {
			if (mb_strlen($key) > 5 && mb_substr($key, 0, 5) == 'HTTP_') {
				$headers[mb_substr($key, 5)] = $val;
			}
		}
		return $headers;
	}
}