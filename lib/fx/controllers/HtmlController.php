<?php
namespace Smart\lib\fx\controllers;

use \Smart\lib\fx\HttpRequest;
use \Smart\lib\fx\HttpResponse;
use \Smart\lib\fx\controllers\Controller;
use \Smart\lib\fx\Js;

class HtmlController extends Controller {

	/**
	 * @var \Smart\lib\fx\HttpRequest
	 */
	public $request;

	/**
	 * @var \Smart\lib\fx\HttpResponse
	 */
	public $response;

	/**
	 * The layout file to use
	 * @var string
	 */
	public $layout = 'blank';

	/**
	 * The folder of action views to render from. Default: /views/<Controller>/
	 * @var string
	 */
	public $viewFolder;

	/**
	 * The action view file to render. Default to route <Action>.html.php
	 * @var string
	 */
	public $viewFile;

	public function __construct(array $route, HttpRequest $request, HttpResponse $response) {
		$response->setContentType('html');
		parent::__construct($route);

		$this->request	= $request;
		$this->response	= $response;

		// prevent folder navigation
		$this->viewFolder	= str_replace('/', '', $route['controller']);
		$this->viewFolder	= str_replace('.', '', $this->viewFolder);

		$this->viewFile		= str_replace('/', '', $route['action']);
		$this->viewFile		= str_replace('.', '', $this->viewFile);

		// set $_GET and $_POST
		$this->data			= array_merge($request->get, $this->data);
		$this->data			= array_merge($request->post, $this->data);
	}

	/**
	 * Render html from the views folder
	 * @return string			The html
	 */
	public function render() {
		extract($this->route);
		extract($this->data);

		/**
		 * Helper for htmlspecialchars
		 * @var array $plainText	Text to turn into HTML
		 */
		$h = function($plainText) {
			return htmlspecialchars($plainText);
		};

		/**
		 * @var string $element		The element file to load
		 * @var array $vars			The variables to inject
		 */
		$el = function($element, $vars) use (&$h, &$el) {
			extract($vars);
			ob_start();
			require PATH_ELEMENT . "{$element}.html.php";
			return ob_get_clean();
		};

		ob_start();
		require PATH_ACTION . "{$this->viewFolder}/{$this->viewFile}.html.php";
		$actionHtml	= ob_get_clean();

		ob_start();
		require PATH_LAYOUT . "{$this->layout}.html.php";
		return ob_get_clean();
	}
}