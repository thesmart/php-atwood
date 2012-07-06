<?php
/**
 * The bootstrap file, for CLI scripts
 */

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
use \Horde\Routes\Horde_Routes_Mapper;

global $SCRIPT_START_TIME;
$SCRIPT_START_TIME	= microtime(true);

if (php_sapi_name() != 'cli') {
	die('Must run from command line');
}

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
ini_set('html_errors', 0);

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
	Env::set('runtime', 'cli');

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

	// include the cli library
	require_once PATH_EXTERNAL . 'php-cli-tools/lib/cli/cli.php';
	\cli\register_autoload();
}
main();