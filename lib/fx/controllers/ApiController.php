<?php
namespace Smart\lib\fx;

use \Smart\lib\fx\controllers\Controller;

class ApiController extends Controller {

	/**
	 * @var \Smart\lib\fx\HttpResponse
	 */
	public $response;

	public function __construct(array $route) {
		parent::__construct($route);
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
		return json_encode($this->getData());
	}
}