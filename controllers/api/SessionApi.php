<?php

namespace Atwood\controllers\api;

use \Monolog\Logger;
use \Atwood\lib\fx\Env;
use \Atwood\models\Session;

/**
 * A set of end-points relating to an end-user's Session
 */
class SessionApi extends \Atwood\lib\fx\controllers\ApiController {

	public function action_get() {
		$this->handleSession();
	}

	public function action_post() {
		$this->session = Session::start();
	}

	/**
	 * Logout.
	 */
	public function action_delete() {
		$session = $this->handleSession();
		if ($session) {
			$session->setLogout();
			$this->session = null;
			$this->data['deleted'] = true;
		} else {
			$this->data['deleted'] = false;
		}
	}
}