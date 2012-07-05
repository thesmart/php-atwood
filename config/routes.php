<?php
/**
 * Hook up any routes here
 * @var \Horde\Routes\Horde_Routes_Mapper $map
 */

use \Smart\lib\fx\Env;

$map->connect('/', array(
	'controller'	=> 'Landing',
	'action'		=> 'start'
));

/***********************************
 * Dynamic helpers
 **********************************/

if (Env::mode('dev')) {
	// only enable these routes when the environment is in developer mode
	$map->connect('/less/:fileName', array(
		'controller'	=> 'Statics',
		'action'		=> 'less'
	));

	// only enable these routes when the environment is in developer mode
	$map->connect('/coffee/:fileName', array(
		'controller'	=> 'Statics',
		'action'		=> 'coffee'
	));
}

/***********************************
 * Unit tests
 **********************************/

if (Env::mode('dev')) {
	// only enable these routes when the environment is in developer mode
	$map->connect('/tests', array(
		'controller'	=> 'UnitTest',
		'action'		=> 'listTests'
	));

	$map->connect('/tests/:ns_0/:ns_1/:ns_2/:ns_3/:ns_4/:ns_5/:ns_6/:ns_7', array(
		'controller'	=> 'UnitTest',
		'action'		=> 'runTest',
		'ns_0'			=> null,
		'ns_1'			=> null,
		'ns_2'			=> null,
		'ns_3'			=> null,
		'ns_4'			=> null,
		'ns_5'			=> null,
		'ns_6'			=> null,
		'ns_7'			=> null
	));
}
