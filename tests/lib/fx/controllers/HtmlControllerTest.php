<?php
namespace Atwood\tests\lib\fx\controllers;

use Atwood\lib\fx\controllers\HtmlController;
use Atwood\controllers\UnitTest;
use Atwood\lib\fx\HttpRequest;
use Atwood\lib\fx\HttpResponse;

class HtmlControllerTest extends \Atwood\lib\test\AtwoodTest {

	public $route;

	/**
	 * @var \Atwood\lib\fx\HttpRequest
	 */
	public $request;

	/**
	 * @var \Atwood\lib\fx\HttpResponse
	 */
	public $response;


	public function setUp() {
		parent::setUp();
		$this->route	= array(
			'controller' => 'UnitTest',
			'action' => 'testView'
		);
		$this->request	= new HttpRequest(array (
			'conf' => 'dev',
			'mode' => 'dev',
			'HTTP_HOST' => 'php-atwood.com',
			'HTTP_CONNECTION' => 'keep-alive',
			'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11',
			'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'HTTP_ACCEPT_ENCODING' => 'gzip,deflate,sdch',
			'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
			'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
			'PATH' => '/usr/local/bin:/usr/bin:/bin',
			'SERVER_NAME' => 'php-atwood.com',
			'SERVER_ADDR' => '10.0.1.10',
			'SERVER_PORT' => '80',
			'REMOTE_ADDR' => '10.0.1.1',
			'DOCUMENT_ROOT' => '/var/www/link',
			'SCRIPT_FILENAME' => '/var/www/link/public/index.php',
			'REMOTE_PORT' => '51561',
			'GATEWAY_INTERFACE' => 'CGI/1.1',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REQUEST_METHOD' => 'GET',
			'QUERY_STRING' => 'foo=bar',
			'REQUEST_URI' => '/tests/lib/fx/controllers/HtmlControllerTest?foo=bar',
			'SCRIPT_NAME' => '/public/index.php',
			'PHP_SELF' => '/public/index.php',
			'REQUEST_TIME' => 1234567890
		));
		$this->response	= new HttpResponse();
	}

	public function testRender() {
		$c		= new MockHtmlController($this->route, $this->request, $this->response);
		$c->action_test();
		$html	= $c->render();
		$this->assertStringStartsWith('<!DOCTYPE html>', $html);
		$this->assertRegExp('/class="alert/', $html);
	}

	public function testLog() {
		$c	= new MockHtmlController($this->route, $this->request, $this->response);
		$this->assertEquals('Atwood\\tests\\lib\\fx\\controllers\\MockHtmlController', $c->log->getName());
	}
}

class MockHtmlController extends \Atwood\lib\fx\controllers\HtmlController {

	public $layout = 'default';

	public function action_test() {
		$this->setData(array('answer' => '#1 Answer!'));
	}
}