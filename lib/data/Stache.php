<?php
namespace Smart\lib\data;

use \Smart\lib\fx\Env;

/**
 * Stache is a handle-bar mustache for Cache.
 * This library uses local PHP memory as a temporary cache.  This is faster than performing redundant DB look-ups.
 * @method static \Smart\lib\data\Stache getInstance($name = null)
 * @method static array.<\Smart\lib\data\Stache> getAllInstances()
 */
class Stache extends \Smart\lib\oop\Singleton {

	/**
	 * The write-back static cache
	 * @var array
	 */
	protected static $cache = array();

	/**
	 * Memcached instance
	 * @var \Memcached
	 */
	protected $mc;

	public function __construct(\Memcached $mc = null) {
		if (!Env::get('stache.memcached.enabled')) {
			// memcache disabled
			$mc = null;
		}
		$this->mc	= $mc;
	}

	/**
	 * Get the namespace cache key
	 * @param $key
	 * @return string
	 */
	protected function getKey($key) {
		if (empty($key)) {
			throw new \InvalidArgumentException('$key can not be empty-string');
		}

		return $key;
	}

	/**
	 * Save data to the Stache
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param int $expiresIn		Optional. Num seconds until expires. Default: 180 seconds
	 * @return void
	 */
	public function set($key, $data, $expiresIn = 180) {
		$key	= $this->getKey($key);
		if ($expiresIn <= 0) {
			$this->del($key);
			return;
		}

		if ($this->mc) {
			$this->mc->set($key, $data, $expiresIn);
		}

		static::$cache[$key]	= $data;
	}

	/**
	 * Delete data from the Stache
	 *
	 * @param string $key
	 * @return void
	 */
	public function del($key) {
		$key	= $this->getKey($key);
		if ($this->mc) {
			$this->mc->delete($key);
		}
		unset(static::$cache[$key]);
	}

	/**
	 * Get data from the Stache
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$key	= $this->getKey($key);
		if (array_key_exists($key, static::$cache)) {
			return static::$cache[$key];
		}

		if ($this->mc) {
			$token	= null;
			$data	= $this->mc->get($key, null, $token);
			if ($token) {
				return $data;
			}
		}

		return null;
	}

	/**
	 * truncates all local cache
	 * @static
	 */
	public static function truncateLocal() {
		static::$cache	= array();
	}

	/**
	 * WARNING: Truncate all memcache data
	 *
	 * @static
	 * @throws \RuntimeException		Can only be called in dev mode
	 */
	public static function truncateMemcached() {
		if (!Env::mode('dev')) {
			throw new \RuntimeException('Can not call Stache::truncate unless in dev mode');
		}
	}

	/**
	 * WARNING: Truncate all cache data
	 * @static
	 * @throws \RuntimeException		Can only be called in dev mode
	 */
	public static function truncateAll() {
		self::truncateMemcached();
		self::truncateLocal();
	}
}