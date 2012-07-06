<?php
/**
 * Setup logging infrastructure
 */

use \Atwood\lib\fx\Env;
use Monolog\Logger;
use Monolog\Handler\PhpErrorLogHandler;
use Monolog\Handler\StreamHandler;

global $_LOGS, $_STD_OUT, $_STD_ERR;

$_LOGS		= array();
$_STD_OUT	= fopen('php://stdout', 'w');
$_STD_ERR	= fopen('php://stderr', 'w');


/**
 * Get a Logger instance for logging
 * @param string $name		The name of the log
 * @return Monolog\Logger
 */
function getLogger($name) {
	global $_LOGS, $_STD_OUT, $_STD_ERR;
	if (isset($_LOGS[$name])) {
		return $_LOGS[$name];
	}

	$_LOGS[$name]	= new Logger($name);

	// setup preferred output method
	switch (Env::get('runtime')) {
		case 'cli':
			$stdOutHandler	= new StreamHandler($_STD_OUT, Logger::DEBUG, true);
			$strErrHandler	= new StreamHandler($_STD_ERR, Logger::ERROR, false);

			$_LOGS[$name]->pushHandler($stdOutHandler);
			$_LOGS[$name]->pushHandler($strErrHandler);

			break;
		case 'http':
			$_LOGS[$name]	= new Logger($name);
			$_LOGS[$name]->pushHandler(new PhpErrorLogHandler(Env::get('logger.level')));
			break;
	}

	return $_LOGS[$name];
}

// create a log channel
$log = getLogger('bootstrap');
