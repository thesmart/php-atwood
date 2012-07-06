<?php

namespace Atwood\controllers;

use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\exception\ApiException;
use \Monolog\Logger;
use \Less\lessc;

/**
 * Landing pages for users.
 */
class Landing extends \Atwood\lib\fx\controllers\HtmlController {

	public $layout = 'default';

	/**
	 * The starting page for an anonymous user "off the street"
	 * @return void
	 */
	public function action_start() {
	}
}