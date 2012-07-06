<?php

namespace Atwood\tests\lib\fx;

use \Atwood\lib\fx\exception\ApiException;
use \Atwood\lib\fx\Env;

class EnvTest extends \Atwood\lib\test\AtwoodTest {

	/**
	 * @var \Atwood\lib\fx\Env
	 */
	private $env = null;

	protected function setUp() {
		parent::setUp();

		// save current env
		$this->env	= Env::getInstance();
	}

	public function testNewInstance() {
		$myEnv = new Env('test', array('foobar'=>'hello'));
		$this->assertEquals($this->env, Env::getInstance());
		$this->assertNotEquals($this->env, $myEnv);

		Env::setInstance($myEnv);
		$this->assertNotEquals($this->env, Env::getInstance());
		$this->assertEquals(Env::getInstance(), $myEnv);

		Env::clearInstance();
		$this->assertFalse(Env::hasInstance());
	}

	public function testAccessors() {
		Env::setInstance(new Env('test', array('foobar'=>'hello')));

		$this->assertTrue(Env::has('foobar'));
		$this->assertFalse(Env::has('barbat'));

		$this->assertEquals('hello', Env::get('foobar'));
		$this->assertNull(Env::get('barbat'), 'unset key should be null');

		Env::set('foobar', 'goodbye');
		$this->assertEquals('goodbye', Env::get('foobar'), 'set value should have changed');
		Env::set('barbat', 'hello');
		$this->assertEquals('hello', Env::get('barbat'));

		$this->assertEquals('test', Env::name());
		$this->assertTrue(Env::is('test'));
		$this->assertFalse(Env::mode('dev'));

		Env::set('mode', 'dev');
		$this->assertTrue(Env::mode('dev'));
		$this->assertEquals('dev', Env::mode());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testNoInstance() {
		Env::clearInstance();
		Env::set('test', 'test');
	}
}