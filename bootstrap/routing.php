<?php
/**
 * The routes that link URL to Controller
 */

use \Horde\Routes\Horde_Routes_Mapper;
use \Smart\lib\Url;
use Smart\lib\fx\Env;

// set default url parameters
Url::$DEFAULT_SCHEME	= Env::get('url.scheme');
Url::$DEFAULT_DOMAIN	= Env::get('url.domain');

$map = new Horde_Routes_Mapper();
// Turn on sub-domain support
$map->subDomains	= true;
// Set the environment
$map->environ		= $_SERVER;
// Set the director to scan for controller classes
$map->directory		= PATH_ROOT . 'controllers';

require_once PATH_CONFIG . 'routes.php';