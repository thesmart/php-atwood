<?php

namespace Atwood\tests\models;

use Atwood\models\Session;

class SessionTest extends \Atwood\lib\test\AtwoodTest {

	public function testBasics() {
		$session	= Session::create('foobar');
		$session->setEmail('foobar');
	}

}