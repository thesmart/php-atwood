<?php
/**
 * The routes that link URL to Controller
 */

use \Smart\lib\fx\statics\JsStatics;
use \Smart\lib\fx\statics\CssStatics;

// Static packages
require_once PATH_CONFIG . 'js_packages.php';
require_once PATH_CONFIG . 'css_packages.php';

// always include the base library
JsStatics::inclHead(JS_BASE);
CssStatics::inclHead(CSS_BASE);
