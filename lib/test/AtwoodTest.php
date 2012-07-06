<?php

namespace Atwood\lib\test;

use \Atwood\lib\fx\Env;
use Atwood\lib\data\MongoConnections;
use Atwood\lib\test\Truncate;

/**
 * Base class for tests in the Atwood project tests folder
 */
abstract class AtwoodTest extends \PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}

	protected function setUp() {
		// setup namespace
		MongoConnections::setNamespace('test');

		// save current env
		Env::setInstance(Env::getInstance(), 'backup');
	}

	protected function tearDown() {
		// restore env
		Env::setInstance(Env::getInstance('backup'));
		MongoConnections::setNamespace('test'); // IMPORTANT!

		// truncate all data
		Truncate::all();

		// restore namespace
		MongoConnections::setNamespace('');
	}
}