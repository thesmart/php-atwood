<?php
namespace Smart\lib\fx;

use \Smart\lib\Url;

/**
 * Encapsulates the response to the web-client
 */
class HttpResponse {

	// [Informational 1xx]
	const HTTP_CONTINUE = 100;
	const HTTP_SWITCHING_PROTOCOLS = 101;
	// [Successful 2xx]
	const HTTP_OK = 200;
	const HTTP_CREATED = 201;
	const HTTP_ACCEPTED = 202;
	const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
	const HTTP_NO_CONTENT = 204;
	const HTTP_RESET_CONTENT = 205;
	const HTTP_PARTIAL_CONTENT = 206;
	// [Redirection 3xx]
	const HTTP_MULTIPLE_CHOICES = 300;
	const HTTP_MOVED_PERMANENTLY = 301;
	const HTTP_FOUND = 302;
	const HTTP_SEE_OTHER = 303;
	const HTTP_NOT_MODIFIED = 304;
	const HTTP_USE_PROXY = 305;
	const HTTP_UNUSED= 306;
	const HTTP_TEMPORARY_REDIRECT = 307;
	// [Client Error 4xx]
	const HTTP_BAD_REQUEST = 400;
	const HTTP_UNAUTHORIZED  = 401;
	const HTTP_PAYMENT_REQUIRED = 402;
	const HTTP_FORBIDDEN = 403;
	const HTTP_NOT_FOUND = 404;
	const HTTP_METHOD_NOT_ALLOWED = 405;
	const HTTP_NOT_ACCEPTABLE = 406;
	const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
	const HTTP_REQUEST_TIMEOUT = 408;
	const HTTP_CONFLICT = 409;
	const HTTP_GONE = 410;
	const HTTP_LENGTH_REQUIRED = 411;
	const HTTP_PRECONDITION_FAILED = 412;
	const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
	const HTTP_REQUEST_URI_TOO_LONG = 414;
	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const HTTP_EXPECTATION_FAILED = 417;
	const HTTP_TEAPOT = 418;
	// [Server Error 5xx]
	const HTTP_INTERNAL_SERVER_ERROR = 500;
	const HTTP_NOT_IMPLEMENTED = 501;
	const HTTP_BAD_GATEWAY = 502;
	const HTTP_SERVICE_UNAVAILABLE = 503;
	const HTTP_GATEWAY_TIMEOUT = 504;
	const HTTP_VERSION_NOT_SUPPORTED = 505;

	private static $messages = array(
		// [Informational 1xx]
		100=>'100 Continue',
		101=>'101 Switching Protocols',
		// [Successful 2xx]
		200=>'200 OK',
		201=>'201 Created',
		202=>'202 Accepted',
		203=>'203 Non-Authoritative Information',
		204=>'204 No Content',
		205=>'205 Reset Content',
		206=>'206 Partial Content',
		// [Redirection 3xx]
		300=>'300 Multiple Choices',
		// don't use
		301=>'301 Moved Permanently',
		// don't use
		302=>'302 Found',
		// indicates that the correct response can be found under a different URI and should be
		// retrieved using a GET method. The specified URI is not a substitute reference for the original resource.
		303=>'303 See Other',
		304=>'304 Not Modified',
		305=>'305 Use Proxy',
		306=>'306 (Unused)',
		// the request should be repeated with another URI, but future requests can still use the original URI
		307=>'307 Temporary Redirect',
		// [Client Error 4xx]
		400=>'400 Bad Request',
		401=>'401 Unauthorized',
		402=>'402 Payment Required',
		403=>'403 Forbidden',
		404=>'404 Not Found',
		405=>'405 Method Not Allowed',
		406=>'406 Not Acceptable',
		407=>'407 Proxy Authentication Required',
		408=>'408 Request Timeout',
		409=>'409 Conflict',
		410=>'410 Gone',
		411=>'411 Length Required',
		412=>'412 Precondition Failed',
		413=>'413 Request Entity Too Large',
		414=>'414 Request-URI Too Long',
		415=>'415 Unsupported Media Type',
		416=>'416 Requested Range Not Satisfiable',
		417=>'417 Expectation Failed',
		418=> '418 I\'m a teapot (RFC 2324)',
		// [Server Error 5xx]
		500=>'500 Internal Server Error',
		501=>'501 Not Implemented',
		502=>'502 Bad Gateway',
		503=>'503 Service Unavailable',
		504=>'504 Gateway Timeout',
		505=>'505 HTTP Version Not Supported'
	);

	private static $mimeTypes = array(
		array('html', 'text/html'),
		array('xhtml', 'application/xhtml+xml'),
		array('xml', array('application/xml', 'text/xml', 'application/x-xml')),
		array('json', array('application/json', 'text/x-json','application/jsonrequest')),
		array('js', array('text/javascript', 'application/javascript', 'application/x-javascript')),
		array('css', 'text/css'),
		array('rss', 'application/rss+xml'),
		array('yaml', array('application/x-yaml', 'text/yaml')),
		array('atom', 'application/atom+xml'),
		array('text', 'text/plain'),
		array('png', 'image/png'),
		array('jpg', 'image/jpeg', 'image/pjpeg'),
		array('gif', 'image/gif'),
		array('form', 'multipart/form-data'),
		array('url-form', 'application/x-www-form-urlencoded'),
		array('csv', 'text/csv'),
		array('file', 'application/octet-stream'),
		array('stream', 'application/octet-stream')
	);

	/**
	 * Status code of the response
	 * @var int
	 */
	public $status = 200;

	/**
	 * Response headers, waiting to be echoed to our output stream
	 * @var array
	 */
	public $headers	= array();

	/**
	 * The response format enum
	 * @var string
	 */
	public $type	= null;

	/**
	 * The response body
	 * @var array
	 */
	public $body	= '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->setStatus(200);
		$this->setContentType('html');
		$this->setCache();
		$this->headers['P3P']	= 'CP="No P3P policy."';
	}

	/**
	 * Set http status
	 * @param int $code
	 * @return void
	 */
	public function setStatus($code) {
		$this->status	= $code;
		$this->headers['HTTP/1.1']	= isset(static::$messages[$code]) ? static::$messages[$code] : static::$messages[static::HTTP_TEAPOT];
	}

	/**
	 * Set the type of the content in the body
	 * @param string $type		'html', 'json', or 'file'
	 * @return void
	 */
	public function setContentType($type) {
		foreach (static::$mimeTypes as $typeSet) {
			if ($typeSet[0] === $type) {
				// match found
				$mime	= is_array($typeSet[1]) ? $typeSet[1][0] : $typeSet[1];
				$this->type						= $type;
				$this->headers['Content-Type']	= $mime;
				return;
			}
		}

		throw new \UnexpectedValueException(sprintf('Did not expect Content-Type: "%s"', $type));
	}

	/**
	 * Set the proper cache headers
	 * @param int $time		Optional. Seconds from now. Default: expires now
	 * @return void
	 */
	public function setCache($time = 0) {
		if ($time > 0) {
			$dateTime						= new \DateTime('', new \DateTimeZone('Etc/UTC'));
			$this->headers['Cache-Control']	= 'private, no-cache, no-store, must-revalidate';
			$this->headers['Expires']		= $dateTime->format(\DateTime::RFC822);
		} else {
			$dateTime						= new \DateTime("+$time seconds", new \DateTimeZone('Etc/UTC'));
			$this->headers['Cache-Control']	= 'private, no-cache, no-store, must-revalidate';
			$this->headers['Expires']		= $dateTime->format(\DateTime::RFC822);
			$this->headers['Pragma']		= 'no-cache';
		}
	}

	/**
	 * Set the response for a redirect
	 *
	 * @param string $url		The valid url to redirect to
	 * @param int $code			Optional. Use 307 or 303 depending on if the redirect is permanent or not
	 * @return void
	 */
	public function setRedirect($url, $code = 307) {
		$this->setStatus($code);
		$this->headers['Location']	= trim($url);
	}

	/**
	 * Echo headers to the output buffer
	 * @return void
	 */
	public function echoHeaders() {
		global $SCRIPT_START_TIME;

		$this->headers['Runtime']	= round(microtime(true) - $SCRIPT_START_TIME, 3);
		foreach ($this->headers as $key => $val) {
			if ($key != 'HTTP/1.1') {
				header(sprintf('%s: %s', $key, $val), true);
			}
		}

		header(sprintf('%s %s', 'HTTP/1.1', $this->headers['HTTP/1.1']), true, $this->status);
	}
}