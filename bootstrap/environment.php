<?php
/**
 * Using environment variables, this file sets Env configuration settings. This enables variations in things like
 * database connections at run-time.  All Env sets are located in /config/sets, and the file name must match the
 * "conf" environment variable.
 */

require_once PATH_ROOT . 'lib/fx/Env.php';
use \Smart\lib\fx\Env;

// load a configuration set
$configToSet	= isset($_SERVER['conf']) ? $_SERVER['conf'] : 'prod';
require_once PATH_CONFIG . 'sets/' . $configToSet . '.php';

// set the mode
Env::set('mode', isset($_SERVER['mode']) ? $_SERVER['mode'] : 'prod');