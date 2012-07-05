<?php
/**
 * The bootstrap file, for Apache http requests
 */

use \Smart\lib\Url;
use \Smart\lib\fx\Env;
use \Horde\Routes\Horde_Routes_Mapper;

global $SCRIPT_START_TIME;
$SCRIPT_START_TIME	= microtime(true);

/**
 * Absolute path root
 */
define('PATH_ROOT', dirname(__FILE__) . '/../');

/**
 * Bootstrap folder
 */
define('PATH_BOOT', PATH_ROOT . 'bootstrap/');

// Load any libraries, on later demand
require_once PATH_BOOT . 'constants.php';

/**
 * This function exists so that the runtime will exist outside of the global scope.
 * @return void
 */
function main() {
	// Load any libraries, on later demand
	require_once PATH_BOOT . 'autoload.php';

	// Setup the environment configuration
	require_once PATH_BOOT . 'environment.php';
	Env::set('runtime', 'http');

	if (Env::mode('dev')) {
		// check environment requirements, but only in dev mode
		require_once PATH_BOOT . 'dependencies.php';
	}

	// Setup any logging tools
	require_once PATH_BOOT . 'logging.php';

	// Route the request
	require_once PATH_BOOT . 'routing.php';

	// Static packages
	require_once PATH_BOOT . 'statics.php';

	// Setup any logging tools
	require_once PATH_CONFIG . 'connections.php';

	// Dispatch the instruction
	require_once PATH_BOOT . 'dispatch.php';
}
main();