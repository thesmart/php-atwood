<?php
namespace Atwood\lib\fx\controllers;

use Atwood\lib\fx\HttpRequest;
use Atwood\lib\fx\HttpResponse;
use Atwood\lib\fx\exception\ApiException;

class ApiController extends HttpController {

	public function __construct(HttpRequest $request, HttpResponse $response) {
		parent::__construct($request, $response);
		$this->response->setContentType('json');
	}

	/**
	 * Get the data that is produced by an action method of this class.
	 * @return array
	 */
	public function getData() {
		return array(
			'data'		=> $this->data,
			'session'	=> $this->session ? $this->session->toApi() : null
		);
	}

	/**
	 * Render the output representation of this Controller
	 * @return string				the json
	 */
	public function render() {
		if ($this->response->isRedirected()) {
			// do not render if redirected
			return '';
		}

		return json_encode($this->getData());
	}
}