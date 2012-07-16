<?php
namespace Atwood\lib\fx\controllers;

use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\controllers\Controller;
use \Atwood\models\Session;
use Atwood\lib\fx\exception\ApiException;

/**
 * A controller that handles HTTP client requests
 */
abstract class HttpController extends Controller {

	/**
	 * The request sent by the client
	 * @var \Atwood\lib\fx\HttpRequest
	 */
	public $request;

	/**
	 * The response to return to the client at the end of the current request process
	 * @var \Atwood\lib\fx\HttpResponse
	 */
	public $response;

	/**
	 * The current user's session, if established by this request
	 * @var Session
	 */
	public $session = null;

	public function __construct(HttpRequest $request, HttpResponse $response) {
		parent::__construct();

		$this->request	= $request;
		$this->response	= $response;
		$this->data		= $request->data;
	}

	/**
	 * Require that a session is established from parameters in the HttpRequest
	 * NOTE: sets $this->session
	 * @param bool $isRequired		Set true if session is required
	 * @return Session
	 * @throws ApiException
	 */
	public function handleSession($isRequired = false) {
		if (!isset($this->data['key'])) {
			if ($isRequired) {
				throw new ApiException('You have been logged out. Please login again.', HttpResponse::HTTP_METHOD_NOT_ALLOWED);
			} else {
				return $this->session = null;
			}
		}

		$session	= Session::resume($this->data['key']);
		if (!$session) {
			if ($isRequired) {
				throw new ApiException('Unable to login. Please try again.', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
			} else {
				return $this->session = null;
			}
		}

		return $this->session = $session;
	}

	/**
	 * Action for default get requests
	 */
	public function action_get() {
		// default
		$this->log->addWarning('Class "%s" did not override method "%s"', get_called_class(), __METHOD__);
	}

	/**
	 * Action for default post requests
	 */
	public function action_post() {
		// default
		$this->log->addWarning('Class "%s" did not override method "%s"', get_called_class(), __METHOD__);
	}

	/**
	 * Action for default put requests
	 */
	public function action_put() {
		// default
		$this->log->addWarning('Class "%s" did not override method "%s"', get_called_class(), __METHOD__);
	}

	/**
	 * Action for default delete requests
	 */
	public function action_delete() {
		// default
		$this->log->addWarning('Class "%s" did not override method "%s"', get_called_class(), __METHOD__);
	}
}