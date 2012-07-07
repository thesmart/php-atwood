<?php
namespace Atwood\lib\fx;

use \Atwood\lib\fx\controllers\Controller;

class ApiController extends Controller {

	/**
	 * @var \Atwood\lib\fx\HttpResponse
	 */
	public $response;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get the data that is produced by an action method of this class.
	 * @return array
	 */
	public function getData() {
		return array(
			'status'	=> 1,
			'data'		=> $this->data,
			'session'	=> null
		);
	}

	/**
	 * Render the output representation of this Controller
	 * @param array $route			The route currently being dispatched
	 * @return string				the json
	 */
	public function render(array $route) {
		if ($this->response->isRedirected()) {
			// do not render if redirected
			return '';
		}

		return json_encode($this->getData());
	}
}