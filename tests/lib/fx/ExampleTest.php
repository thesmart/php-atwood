<?php

namespace Atwood\tests\lib\fx;

use \Atwood\lib\fx\exception\ApiException;

/**
 * This is a test that is generally ignored except that it provides examples for what different test results will look
 * like in the Test Sweet
 * @throws \Exception|\Atwood\lib\fx\exception\ApiException
 *
 */
class ExampleTest extends \Atwood\lib\test\AtwoodTest {

	public function testSuccess() {
		$this->assertTrue(true);
	}

	public function testSuccess1() {
		$this->assertTrue(true);
	}

	public function testSuccess2() {
		$this->assertTrue(true);
	}

	public function testIncomplete() {
		$this->markTestIncomplete('Bleh');
	}

	public function testSkipped() {
		$this->markTestSkipped('skipping through the park');
	}

	public function testFailure() {
		$this->assertTrue(false, 'Houston, we have a problem');
	}

	public function testException() {
		throw new \Exception('throw me over');
	}

	public function testExpectedException() {
		$this->setExpectedException('\\Atwood\\lib\\fx\\exception\\ApiException', 'throw me under');
		throw new ApiException('throw me under', 500);
	}
}