<?php

namespace Smart\lib\oop;

abstract class Singleton {

	/**
	 * An array of named instances
	 * @var array
	 */
	private static $instances = null;

	private static function getNs($name = null) {
		return empty($name) ? get_called_class() : get_called_class() . '::' . $name;
	}

	/**
	 * Is an instance set?
	 * @static
	 * @param string $name		Optional.
	 * @return bool
	 */
	public static function hasInstance($name = null) {
		return static::getInstance($name) == true;
	}

	/**
	 * Singleton pattern
	 * @param string $name		Optional.
	 * @return mixed
	 */
	public static function getInstance($name = null) {
		$name = static::getNs($name);

		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}

		return null;
	}

	/**
	 * @static
	 * @param Singleton $instance
	 * @param string $name			Optional. Set if you want to name this instance.
	 * @return void
	 */
	public static function setInstance(Singleton $instance, $name = null) {
		$name = static::getNs($name);
		self::$instances[$name]	= $instance;

		if (!static::hasInstance()) {
			// set the default instance
			static::setInstance($instance);
		}
	}

	/**
	 * reset the instance to null
	 * @static
	 * @param string $name		Optional.
	 * @return void
	 */
	public static function clearInstance($name = null) {
		$name = static::getNs($name);
		if (isset(self::$instances[$name])) {
			unset(self::$instances[$name]);
		}
	}

	/**
	 * @static
	 * @return array
	 */
	public static function getAllInstances() {
		return self::$instances;
	}
}
