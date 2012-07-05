<?php
/**
 * Support for url construction, mutation, and output depending on content type.
 */

namespace Smart\lib;

class Url {

	public static $DEFAULT_SCHEME = 'http';
	public static $DEFAULT_DOMAIN = array('example', 'com');

	/**
	 * The url scheme
	 * NOTE: not url-encoded
	 *
	 * @var string
	 */
	protected $_scheme;

	/**
	 * all the domain parts that make up this Url
	 * NOTE: not url-encoded
	 *
	 * @var array
	 */
	protected $_domain;

	/**
	 * The port number
	 * NOTE: not url-encoded
	 *
	 * @var int
	 */
	protected $_port = 80;

	/**
	 * all the path parts that make up this Url
	 * NOTE: not url-encoded
	 *
	 * @var array
	 */
	protected $_path = array();

	/**
	 * Whether to use a trailing slash
	 * @var bool
	 */
	protected $_trailingSlash	= true;

	/**
	 * Associative array of query key/value pairs
	 * NOTE: not url-encoded
	 *
	 * @var array
	 */
	protected $_query = array();

	/**
	 * The fragment identifier
	 * NOTE: not url-encoded
	 * 
	 * @var array
	 */
	protected $_fragment = '';

	public function __construct($url = null) {
		$this->_scheme	= Url::$DEFAULT_SCHEME;
		$this->_domain	= Url::$DEFAULT_DOMAIN;

		if ($url) {
			$this->_mutateFromStr($url);
		}
	}

	/**
	 * Mutate the object using a url string
	 * @param string $url
	 */
	protected function _mutateFromStr($url) {
		$urlParts = parse_url($url);
		$this->scheme(isset($urlParts['scheme']) ? $urlParts['scheme'] : null);
		$this->domain(isset($urlParts['host']) ? $urlParts['host'] : null);
		$this->port(isset($urlParts['port']) ? $urlParts['port'] : null);
		$this->path(isset($urlParts['path']) ? $urlParts['path'] : null);
		$this->query(isset($urlParts['query']) ? $urlParts['query'] : null);
		$this->fragment(isset($urlParts['fragment']) ? $urlParts['fragment'] : null);
	}

	/**
	 * Enforce deep-clone
	 * @return void
	 */
	public function __clone() {
		$this->_mutateFromStr($this->__toString());
	}

	/**
	 * get or set the url scheme (aka: protocol)
	 * e.g. 'http', 'ftp', 'etc'
	 *
	 * @param string|null $scheme     The scheme to set
	 * @return string        The scheme set
	 */
	public function scheme($scheme = null) {
		if (empty($scheme)) {
			return $this->_scheme;
		}

		$this->_scheme  = str_replace('://', '', $scheme);
		if ($this->_scheme == 'https') {
			$this->port(443);
		} else if ($this->_scheme == 'http') {
			$this->port(80);
		}

		return $this->_scheme;
	}

	/**
	 * Set or get the domain, as an array.
	 * e.g. sub.example.com would be array('sub', 'example', 'com')
	 *
	 * @param array|string|null $domain     A domain or set of (sub)domains
	 * @return array        The domain set
	 */
	public function domain($domain = null) {
		if (empty($domain)) {
			return $this->_domain;
		} else if (is_array($domain)) {
			$this->_domain  = $domain;
			return $this->_domain;
		} else if (is_string($domain)) {
			$this->_domain  = explode('.', trim($domain, '.'));
			return $this->_domain;
		}

		$this->_domain  = implode(strval($domain));
		return $this->_domain;
	}

	/**
	 * Alias for the domain method
	 *
	 * @param array|string|null $host     A host or set of (sub)domains
	 * @return array        The domain set
	 */
	public function host($host = null) {
		return $this->domain($host);
	}

	/**
	 * Get or set the top-level-domain.
	 * e.g. "com" or "net" or "org", etc.
	 *
	 * @param string|null $tld      The top-level-domain.
	 * @return string|null
	 */
	public function tld($tld = null) {
		if (empty($tld)) {
			if (empty($this->_domain)) {
				return null;
			}
			
			return $this->_domain[count($this->_domain) - 1];
		}

		// set the top-most domain
		$this->_domain[count($this->_domain) - 1] = trim($tld, '.');
		return $this->_domain[count($this->_domain) - 1];
	}

	/**
	 * Set or get the port value.
	 *
	 * @param int|null $port
	 * @return int
	 */
	public function port($port = null) {
		if (is_null($port)) {
			return $this->_port;
		}

		$this->_port = intval($port);
		if ($this->_port <= 0 || $this->_port > 65536) {
			$this->_port = 80;
		}

		return $this->_port;
	}

	/**
	 * Set or get the path
	 * e.g. '/api/User/view' or array('api', 'User', 'view')
	 *
	 * @param array|string|null $path       The path to set.
	 * @return array    The path
	 */
	public function path($path = null) {
		if (is_null($path)) {
			return $this->_path;
		} else if (empty($path)) {
			$this->_trailingSlash	= true;
			$this->_path			= array();
			return $this->_path;
		} else if (is_string($path)) {
			// determine trailing slash
			$this->_trailingSlash	= mb_substr($path, -1, 1) === '/';
			// remove beginning and trailing slashes
			$path = trim($path);
			$path = trim($path, '/');
			if (empty($path)) {
				$this->_path = array();
			} else {
				$this->_path = explode('/', $path);
				foreach ($this->_path as &$piece) {
					$piece	= rawurldecode($piece);
				} unset ($piece);
			}
		} else if (is_array($path)) {
			// determine trailing slash
			$this->_trailingSlash	= mb_substr($path[count($path) - 1], -1, 1) === '/';
			$this->_path = $path;
		}

		return $this->_path;
	}

	/**
	 * Get the path as a string
	 * e.g. '/api/awesome/?foo=bar#foobar'
	 *
	 * @param string $withRespectTo     Optional. If passed, will be a relative path "with respect to" an origin
	 *
	 * @return string
	 */
	public function pathRelative($withRespectTo = null) {
		if (is_null($withRespectTo)) {
			return self::pathToString($this->path(), $this->_trailingSlash);
		}

		$url = new Url($withRespectTo);
		$pathThis = $this->path();
		$pathThat = $url->path();

		$sameCount = 0;
		for ($i = 0; $i < count($pathThat); ++$i) {
			if (!isset($pathThat[$i]) || !isset($pathThis[$i])) {
				break;
			}

			if ($pathThis[$i] == $pathThat[$i]) {
				++$sameCount;
			}
		}

		if ($sameCount === 0) {
			// paths are unrelated
			return self::pathToString($pathThis, $this->_trailingSlash);
		}

		if (count($pathThis) == $sameCount && count($pathThat) == $sameCount) {
			// relative path same
			return '';
		} else if (count($pathThis) > $sameCount) {
			// cut off all the portion of the path that is the same
			$pathRel = array();

			$countNested = count($pathThat) - $sameCount;
			for ($i = 0; $i < $countNested; ++$i) {
				$pathRel[] = '..';
			}

			array_splice($pathThis, 0, $sameCount, $pathRel);
			return ltrim(self::pathToString($pathThis, $this->_trailingSlash), '/');
		}

		// relative path is under current folder
		for ($i = 0; $i < $sameCount; ++$i) {
			$pathThat[$i] = '..';
		}

		return ltrim(self::pathToString($pathThat, $this->_trailingSlash), '/');
	}

	/**
	 * Set or get the query
	 * e.g. the part of the url after the '?' but before the '#' or end-of-string
	 *
	 * @param array|string|null $query  The query to set.
	 * @return array
	 */
	public function query($query = null, $replace = false) {
		if (empty($query)) {
			if ($replace) {
				$this->_query	= array();
			}
			return $this->_query;
		} else if (is_string($query)) {
			$queryArr	= array();
			parse_str($query, $queryArr);
			$query		= $queryArr;
		}

		if ($replace) {
			$this->_query	= array();
		}

		self::_queryToStr($query);
		foreach ($query as $key => $val) {
			if (is_null($val)) {
				if ($replace) {
					$this->_query[$key]	= '';
				} else {
					// clear the query key
					unset($this->_query[$key]);
				}
			} else {
				$this->_query[$key] = $val;
			}
		}

		return $this->_query;
	}

	/**
	 * Get the query string
	 * @param array|string|null $query  The query to set
	 * @param bool $replace				Set true to replace the query string rather than to augment it
	 * @return string
	 */
	public function queryStr($query = null, $replace = false) {
		$query = $this->query($query, $replace);
		return http_build_query($query);
	}

	/**
	 * Get or set the fragment
	 * e.g. the part of the url after the '#'
	 *
	 * @param array|string|null $query  The query to set.
	 * @return array
	 */
	public function fragment($query = null, $replace = false) {
		if (is_null($query)) {
			if ($replace) {
				$this->_fragment	= array();
			}
			return $this->_fragment;
		} else if (is_string($query)) {
			$queryArr	= array();
			parse_str($query, $queryArr);
			$query		= $queryArr;
		}

		if ($replace) {
			$this->_fragment	= array();
		}

		self::_queryToStr($query);
		foreach ($query as $key => $val) {
			if (is_null($val)) {
				if ($replace) {
					$this->_fragment[$key]	= '';
				} else {
					// clear the query key
					unset($this->_fragment[$key]);
				}
			} else {
				$this->_fragment[$key] = $val;
			}
		}

		return $this->_fragment;
	}

	/**
	 * Get the absolute string representation of the Url object.
	 * e.g. 'https://www.example.com/?foo=bar#foobar'
	 * 
	 * @return string
	 */
	public function __toString() {
		$port = ':' . $this->_port;
		if ($this->_scheme == 'http' && $this->_port == 80) {
			// revert to default
			$port = '';
		} else if ($this->_scheme == 'https' && $this->_port == 443) {
			// revert to default
			$port = '';
		}

		$path = '/';
		if (!empty($this->_path)) {
			$pieces		= array();
			$skipDots	= true;
			foreach ($this->_path as $piece) {
				if ($skipDots && $piece === '..') {
					continue;
				}
				$skipDots	= false;
				$pieces[]	= rawurlencode($piece);
			}

			if (!empty($pieces)) {
				$path = '/' . implode('/', $pieces);

				if ($this->_trailingSlash) {
					$path .= '/';
				}
			}
		}

		return implode('', array(
			$this->_scheme,
			'://',
			(empty($this->_domain) ? 'example.com' : implode('.', $this->_domain)),
			$port,
			$path,
			(empty($this->_query) ? '' : '?' . http_build_query($this->_query)),
			(empty($this->_fragment) ? '' : '#' . http_build_query($this->_fragment))
		));
	}

	/**
	 * Utility function to turn all query data into string data
	 *
	 * @param string& $queryStr
	 */
	protected static function _queryToStr(array& $query) {
		foreach ($query as $key => &$val) {
			if (is_scalar($key) && !is_string($key)) {
				$scalarKeys[] = $key;
			}

			if (is_scalar($val) && !is_string($val)) {
				$val = strval($val);
			} else if (is_array($val)) {
				self::_queryToStr($val);
			}
		} unset($val);
	}

	/**
	 * Turn a path array into a string
	 * @return string
	 * @static
	 */
	public static function pathToString(array $path, $useTrailingSlash = true) {
		$pieces		= array('');
		foreach ($path as $piece) {
			$pieces[]	= rawurlencode($piece);
		}

		$path		= implode('/', $pieces);
		if ($useTrailingSlash) {
			$path	.= '/';
		}
		return  $path;
	}
}