<?php
/**
 * Constants that are not runtime
 */

/**
 * Configurations folder
 */
define('PATH_CONFIG', PATH_ROOT . 'config/');

/**
 * External libraries folder
 */
define('PATH_EXTERNAL', PATH_ROOT . 'external/');

/**
 * Atwood's "native" library folder
 */
define('PATH_LIBRARY', PATH_ROOT . 'lib/');

/**
 * Public http folder that apache serves from
 */
define('PATH_PUBLIC', PATH_ROOT . 'public/');

/**
 * Public http folder
 */
define('PATH_STATIC', PATH_ROOT . 'static/');

/**
 * Unit Tests folder
 */
define('PATH_TESTS', PATH_ROOT . 'tests/');

/**
 * Views folder
 */
define('PATH_ELEMENT', PATH_ROOT . 'views/elements/');

/**
 * Views folder
 */
define('PATH_LAYOUT', PATH_ROOT . 'views/layouts/');

/**
 * Views folder
 */
define('PATH_ACTION', PATH_ROOT . 'views/actions/');

/**
 * Elements folder
 */
define('PATH_ELEMENTS', PATH_ROOT . 'views/elements/');

/**
 * Get the name of a class
 * @param string $calledClass
 * @return string
 */
function classNameFromPath($calledClass) {
	preg_match('/^.*\\\(.+)$/', $calledClass, $matches);
	return $matches[1];
}

/**
 * Flip all slashes forward
 * e.g. \this\is\a\path --> /this/is/a/path
 * @param $path
 * @return string
 */
function slashesFlipForward($path) {
	return preg_replace('#\\\#', '/', $path);
}

/**
 * Flip all slashes backward
 * e.g. /this/is/a/path --> \this\is\a\path
 * @param $path
 * @return string
 */
function slashesFlipBackward($path) {
	return preg_replace('#/#', '\\', $path);
}