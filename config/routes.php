<?php
/**
 * Hook up any routes here
 * @var \Horde\Routes\Horde_Routes_Mapper $map
 */

use Atwood\lib\fx\Env;
use Atwood\lib\Atwood;
use Atwood\lib\fx\controllers\HttpController;

$atWood	= new Atwood();

$atWood->mapHtml('/')->setClosure(function(HttpController $controller) {
	$controller->setData(array('msg' => 'Hello World!'));
})->setView('Landing/start');

/***********************************
 * Dynamic helpers
 **********************************/

if (Env::mode('dev')) {
	// only enable these routes when the environment is in developer mode
	$atWood->map('/less/:fileName', 'Statics', 'less');

	// only enable these routes when the environment is in developer mode
	$atWood->map('/coffee/:fileName', 'Statics', 'coffee');
}

/***********************************
 * Unit tests
 **********************************/

if (Env::mode('dev')) {
	// only enable these routes when the environment is in developer mode
	Atwood::setDefaultLayout('tests');
	$atWood->map('/tests', 'UnitTest', 'listTests');
	$atWood->map('/tests/:ns_0/(:ns_1(/:ns_2(/:ns_3(/:ns_4(/:ns_5(/:ns_6(/:ns_7)))))))', 'UnitTest', 'runTest');
}
