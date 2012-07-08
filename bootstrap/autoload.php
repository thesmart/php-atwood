<?php
/**
 * Load scripts using the namespace as a file path
 */

spl_autoload_register(function($class) {
	$class		= preg_replace('/\\\\/', '/', $class);
	$path		= PATH_ROOT . $class . '.php';

	if (strstr($class, 'Atwood') !== false) {
		$classPath	= explode('/', $class);
		array_shift($classPath);
		$classPath	= implode('/', $classPath);
		$path		= PATH_ROOT . $classPath . '.php';;
		include_once $path;
	} else if (strstr($class, 'Horde') !== false) {
		if (strstr($class, 'Horde/') === false) {
			$classPath		= explode('_', $class);
			$class			= implode('/', $classPath) . '/' . $class;
		}
		$path		= PATH_EXTERNAL . $class . '.php';
		include_once $path;
	}
});

if (isset($_SERVER['conf']) && $_SERVER['conf'] == 'dev' ) {
	// run phpunit autoloader
	require_once 'PHPUnit/Autoload.php';
}