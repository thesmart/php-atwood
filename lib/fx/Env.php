<?php
namespace Smart\lib\fx;

/**
 * Handles configuration switching
 */
class Env extends \Smart\lib\oop\Singleton {

	/**
	 * The name of this Env instance
	 * @var string
	 */
	public $name;

	/**
	 * An array of configuration settings, like database connection strings etc.
	 * @var array
	 */
	public $data;

	public function __construct($name, array $data) {
		$this->name		= $name;
		$this->data		= $data;
	}

	/**
	 * Get the current instance name
	 * @static
	 * @return null|string
	 */
	public static function name() {
		/** @var $env Env */
		$env = static::getInstance();
		return $env->name;
	}

	/**
	 * Check if a Env name is the current Env
	 * @static
	 * @param string $name				The name of the configuration
	 * @return bool
	 */
	public static function is($name) {
		/** @var $env Env */
		$env = static::getInstance();
		return $name === $env->name;
	}

	/**
	 * Get the current mode, or test if a specific mode is set
	 * @static
	 * @param string|null $mode		Optional. If set, compare modes and return a bool.  If unset, return the current mode.
	 * @return string|bool
	 */
	public static function mode($mode = null) {
		/** @var $env Env */
		$env = static::getInstance();

		$currentMode	= $env::get('mode');
		if (is_string($mode)) {
			return $mode === $currentMode;
		}

		return $currentMode;
	}

	/**
	 * Has a configuration variable set
	 *
	 * @static
	 * @param string $key		The name of the configuration variable to get.
	 * @return bool
	 */
	public static function has($key) {
		/** @var $env Env */
		$env = static::getInstance();
		return isset($env) && array_key_exists($key, $env->data);
	}

	/**
	 * Get an configuration variable
	 *
	 * @static
	 * @param string $key		The name of the configuration variable to get.
	 * @return mixed
	 */
	public static function get($key) {
		/** @var $env Env */
		$env = static::getInstance();

		if (static::has($key)) {
			return $env->data[$key];
		}

		return null;
	}

	/**
	 * @static
	 * @param string $key		The name of the configuration variable to set.
	 * @param mixed $data			The data to set
	 * @return mixed
	 */
	public static function set($key, $data) {
		if (!static::hasInstance()) {
			throw new \RuntimeException(sprintf('Cannot set Env variable "%s" because there is no Env instance.', $key));
		}

		/** @var $env Env */
		$env = static::getInstance();
		$env->data[$key]	= $data;
		return $data;
	}
}