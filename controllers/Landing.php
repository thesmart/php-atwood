<?php

namespace Smart\controllers;

use \Smart\lib\fx\HttpRequest;
use \Smart\lib\fx\HttpResponse;
use \Smart\lib\fx\exception\ApiException;
use \Monolog\Logger;
use \Less\lessc;

/**
 * Landing pages for users.
 */
class Landing extends \Smart\lib\fx\controllers\HtmlController {

	public $layout = 'default';

	/**
	 * The starting page for an anonymous user "off the street"
	 * @return void
	 */
	public function action_start() {
	}
}