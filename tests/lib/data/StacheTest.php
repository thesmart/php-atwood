<?php

namespace Atwood\tests\lib\data;

use \Atwood\lib\data\Stache;
use \Atwood\lib\fx\exception\ApiException;
use \Atwood\lib\fx\Env;

class StacheTest extends \Atwood\lib\test\AtwoodTest {

	public function testBootstrap() {
		$this->assertTrue(Stache::hasInstance(), 'Stache instance not set during bootstrap');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testBlankKey() {
		$stache = Stache::getInstance();
		$stache->set('', 'test');
	}

	public function testCrud() {
		$stache = Stache::getInstance();
		$val = $stache->get('testCrud');
		$this->assertNull($val);

		$id	= uniqid();
		$stache->set('testCrud', $id);
		$this->assertEquals($id, $stache->get('testCrud'));

		$stache->del('testCrud');
		$this->assertNull($stache->get('testCrud'));

		$id	= uniqid();
		$stache->set('testCrud', $id);
		$this->assertEquals($id, $stache->get('testCrud'));

		// test memcached
		Stache::truncateLocal();
		$this->assertEquals($id, $stache->get('testCrud'));

		$stache->set('testCrud', $id, -1);
		$this->assertNull($stache->get('testCrud'));
	}

	public function testCrudNoMemcached() {
		Env::set('stache.memcached.enabled', false);

		$stache = new Stache(null);
		$val = $stache->get('testCrud');
		$this->assertNull($val);

		$id	= uniqid();
		$stache->set('testCrud', $id);
		$this->assertEquals($id, $stache->get('testCrud'));

		$stache->del('testCrud');
		$this->assertNull($stache->get('testCrud'));

		$id	= uniqid();
		$stache->set('testCrud', $id);
		$this->assertEquals($id, $stache->get('testCrud'));

		// test memcached
		Stache::truncateLocal();
		$this->assertNull($stache->get('testCrud'));
		$stache->set('testCrud', $id, -1);
		$this->assertNull($stache->get('testCrud'));

		Env::set('stache.memcached.enabled', true);
	}

	public function testSpecialPurpose() {
		$stache	= Stache::getInstance();
		$rc		= new \ReflectionClass($stache);
		$mcr	= $rc->getProperty('mc');
		$mcr->setAccessible(true);

		$stache	= new Stache($mcr->getValue($stache));
		$mcr->setAccessible(false);
	}
}
