<?php

namespace Atwood\tests\lib\fx\controllers;

use \Atwood\lib\fx\controllers\Controller;

class ControllerTest extends \Atwood\lib\test\AtwoodTest {

	public function testData() {
		$c = new MockController(array());
		$c->setData(array(
			'a' => ord('a'),
			'b' => ord('b'),
			'c' => ord('c')
		));

		$this->assertEquals(ord('a'), $c->data['a']);
		$this->assertEquals(ord('b'), $c->data['b']);

		$c->setData(array('b' => 888));

		$this->assertEquals(ord('a'), $c->data['a']);
		$this->assertEquals(888, $c->data['b']);
	}

	public function testLog() {
		$c = new MockController(array());
		$this->assertEquals('Atwood\\tests\\lib\\fx\\controllers\\MockController', $c->log->getName());
	}
}

class MockController extends Controller {

}