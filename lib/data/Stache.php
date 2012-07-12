<?php
namespace Atwood\lib\data;

use \Atwood\lib\fx\Env;

/**
 * Stache is a handle-bar mustache for Cache.
 * This library uses local PHP memory as a temporary cache.  This is faster than performing redundant DB look-ups.
 * @method static \Atwood\lib\data\Stache getInstance($name = null)
 * @method static array.<\Atwood\lib\data\Stache> getAllInstances()
 */
class Stache extends \Atwood\lib\oop\GlobalSingleton {

	/**
	 * The write-back static cache
	 * @var array
	 */
	protected static $cache = array();

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
	 * @return void
	 */
	public function set($key, $data) {
		$key	= $this->getKey($key);
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

		return null;
	}

	/**
	 * truncates all cache
	 * @static
	 */
	public static function truncate() {
		static::$cache	= array();
	}
}