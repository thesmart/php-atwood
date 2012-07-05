<?php
/**
 * This script checks to see that all required settings are correct in order to properly run php-atwood
 */

namespace Smart\bootstrap;

/**
 * Used to denote requirements failures
 */
class AtwoodRequirement extends \Exception {
}

$propertyMap	= array(
	// multibyte character / unicode support
	array('mbstring.language',				'Neutral',		'Set default language to "Neutral"'),
	array('mbstring.internal_encoding',		'UTF-8',		'Set default internal encoding to "UTF-8"'),
	array('mbstring.encoding_translation',	1,				'HTTP input encoding translation is enabled'),
	array('mbstring.http_input',			'auto',			'Set HTTP input character set dectection to auto'),
	array('mbstring.http_output',			'UTF-8',		'Set HTTP output encoding to UTF-8'),
	array('mbstring.detect_order',			'auto'	,		'Set default character encoding detection order to auto'),
	array('mbstring.substitute_character',	''	,			'Changes unsupported characters to a specific char'),
	array('default_charset',				'UTF-8',		'Default character set for auto content type header'),

	// xdebug support
	array('xdebug.coverage_enable',			true,			'Allows xdebug to collect code-coverage info'),
	array('xdebug.remote_enable',			true,			'Allows for clients to debug code running on the server'),
//	array('xdebug.remote_connect_back',		true,			'Pretty insecure, but makes it easier to debug on restrictive networks'),

	// mongo settings
	array('mongo.native_long',				true,			'Allows mongo driver to store 64-bit numbers'),
	array('mongo.cmd',						':',			'Both PHP and Mongo use $ specially in strings. This allows an alternative.'),
	array('mongo.cmd',						':',			'Both PHP and Mongo use $ specially in strings. This allows an alternative.'),

	// xhprof setings
//	array('xhprof.output_dir',				true,			'The location where xhprof will dump its reports'),
);

$extensionsMap	= array(
	'curl'				=> 'Used for calling external web services',
	'mcrypt'			=> 'Used for encryption and security',
	'memcached'			=> 'Used to increase performance',
	'mongo'				=> 'Used for persistent storage, i.e. the database',
	'xdebug'			=> 'Used for unit testing, debugging, and code coverage analysis',
//	'xhprof'			=> 'Used for profiling and benchmarking code',
	'xmlrpc'			=> 'Used for xml API access to remote procedures',
);

// check for required extensions
foreach ($extensionsMap as $ext => $info) {
	if (!extension_loaded($ext)) {
		throw new AtwoodRequirement(sprintf('Extension required: %s - %s', $ext, $info));
	}
}

// check all ini properties
foreach ($propertyMap as $mapping) {
	list($property, $value, $info) = $mapping;
	$actual	= ini_get($property);

	if ($actual != $value) {
		if (empty($actual)) {
			throw new AtwoodRequirement(sprintf('%s: Property is not set. %s', $property, $info));
		}
		throw new AtwoodRequirement(sprintf('%s: expected value "%s" but instead got %s', $property, $value, $actual));
	}

	if ($property == 'xhprof.output_dir') {
		if (!file_exists($value)) {
			throw new AtwoodRequirement(sprintf('%s: folder "%s" does not exist', $property, $value));
		} else if (!is_readable($value)) {
			throw new AtwoodRequirement(sprintf('%s: folder "%s" is not readable', $property, $value));
		} else if (!is_writeable($value)) {
			throw new AtwoodRequirement(sprintf('%s: folder "%s" is not writable', $property, $value));
		}
	}
}
