<?php

namespace Smart\tests\lib\oop;

use \Smart\lib\oop\Singleton;

class SingletonTest extends \Smart\lib\test\AtwoodTest {

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

		SingletonMock::setInstance(new SingletonMock(), 'foobar');
		$this->assertEquals($obj, Singleton::getInstance('foobar'));
		$this->assertEquals($obj, Singleton::getInstance());

		$this->assertTrue(SingletonMock::hasInstance());
		$this->assertTrue(SingletonMock::hasInstance('foobar'));

		SingletonMock::clearInstance();
		$this->assertFalse(SingletonMock::hasInstance());
		$this->assertTrue(SingletonMock::hasInstance('foobar'));
		SingletonMock::clearInstance('foobar');
		$this->assertFalse(SingletonMock::hasInstance('foobar'));
	}
}

class SingletonMock extends Singleton {
}