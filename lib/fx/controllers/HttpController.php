<?php
namespace Atwood\lib\fx\controllers;

use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\controllers\Controller;

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

	public function __construct(HttpRequest $request, HttpResponse $response) {
		parent::__construct();

		$this->request	= $request;
		$this->response	= $response;

		// set $_GET and $_POST
		$this->data			= array_merge($request->get, $this->data);
		$this->data			= array_merge($request->post, $this->data);
	}
}