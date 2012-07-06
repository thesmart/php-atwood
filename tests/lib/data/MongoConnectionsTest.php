<?php

namespace Atwood\tests\lib\data;

use Atwood\lib\data\MongoConnections;
use \Atwood\lib\fx\Env;

class MongoConnectionsTest extends \Atwood\lib\test\AtwoodTest {

	public function testConnect() {
		$connections	= Env::get('mongo');
		$this->assertGreaterThanOrEqual(2, count($connections));

		$attributes	= $connections[0];
		$mongo	= MongoConnections::connect($attributes['name'], $attributes['connection_str'], $attributes['is_replica_set']);
		$this->assertInstanceOf('\\Mongo', $mongo);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testMongo() {
		$mongo	= MongoConnections::getMongo('main');
		$this->assertInstanceOf('\\Mongo', $mongo);

		$mongo	= MongoConnections::getMongo(uniqid());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testDb() {
		$allDbs	= MongoConnections::getAllDbs();
		$this->assertInternalType('array', $allDbs);

		if (isset($allDbs['main'])) {
			$mongoDb	= MongoConnections::getDb('main', true);
			$this->assertInstanceOf('\\MongoDB', $mongoDb);
		}

		$this->assertFalse(MongoConnections::dbExists(uniqid()));
		$this->assertTrue(MongoConnections::dbExists('main'));

		$testDb	= MongoConnections::getDb('test');
	}

	public function testCol() {
		$col	= MongoConnections::getCol('main', 'unitTest', true);
		$this->assertInstanceOf('\\MongoCollection', $col);
	}
}