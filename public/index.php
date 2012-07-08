<?php
/**
 * The bootstrap file, for Apache http requests
 */

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
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

	// Load vendor libraries
	require PATH_ROOT . 'vendor/autoload.php';

	// Setup the environment configuration
	require_once PATH_BOOT . 'environment.php';

	Env::set('runtime', 'http');
	Url::$DEFAULT_SCHEME	= Env::get('url.scheme');
	Url::$DEFAULT_DOMAIN	= Env::get('url.domain');

	if (Env::mode('dev')) {
		// check environment requirements, but only in dev mode
		require_once PATH_BOOT . 'dependencies.php';
	}

	// Setup any logging tools
	require_once PATH_BOOT . 'logging.php';

	// Static packages
	require_once PATH_BOOT . 'statics.php';

	// Setup any logging tools
	require_once PATH_CONFIG . 'connections.php';

	// Setup routes, mapping them to the controllers
	require_once PATH_CONFIG . 'routes.php';

	// Dispatch the instruction
	require_once PATH_BOOT . 'dispatch.php';
}
main();