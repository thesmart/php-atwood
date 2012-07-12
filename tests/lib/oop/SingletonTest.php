<?php

namespace Atwood\tests\lib\oop;

use \Atwood\lib\oop\Singleton;

class SingletonTest extends \Atwood\lib\test\AtwoodTest {

	public function testBasics() {
		$obj = SingletonMock::getInstance();
		$this->assertNull($obj);

		SingletonMock::setInstance(new SingletonMock());
		$this->assertEquals($obj, Singleton::getInstance());

		$this->assertTrue(SingletonMock::hasInstance());

		SingletonMock::clearInstance();
		$this->assertFalse(SingletonMock::hasInstance());
	}

	public function testNamed() {
		$obj = SingletonMock::getInstance('foobar');
		$this->assertNull($obj);

		$obj = new SingletonMock();
		SingletonMock::setInstance($obj, 'foobar');
		$this->assertEquals($obj, SingletonMock::getInstance('foobar'));
		$this->assertNull(SingletonMock::getInstance());
		$this->assertFalse(SingletonMock::hasInstance());

		SingletonMock::setInstance(new SingletonMock(), 'barbat');
		$this->assertTrue(SingletonMock::hasInstance('barbat'));

		$this->assertTrue(SingletonMock::hasInstance('foobar'));
		SingletonMock::clearInstance('foobar');
		$this->assertFalse(SingletonMock::hasInstance('foobar'));

		$this->assertTrue(SingletonMock::hasInstance('barbat'));
		Singleton::clearAllInstances();
		$this->assertFalse(SingletonMock::hasInstance('barbat'));
	}

	public function testTruncateA() {
		for ($i = 0; $i < 15; ++$i) {
			SingletonMock::setInstance(new SingletonMock(), $i);
		}
		$this->assertEquals(15, count(SingletonMock::getAllInstances()));
	}

	/**
	 * @depends testTruncateA
	 */
	public function testTruncateB() {
		$this->assertEquals(0, count(SingletonMock::getAllInstances()));
	}
}

class SingletonMock extends Singleton {
}