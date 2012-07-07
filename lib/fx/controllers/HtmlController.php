<?php
namespace Atwood\lib\fx\controllers;

use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\controllers\HttpController;

/**
 * A controller that handles web-browser requests for HTML5 responses
 */
class HtmlController extends HttpController {
	/**
	 * The layout file to use, see the /views/layouts folder
	 * @var string
	 */
	public $layout = 'blank';

	/**
	 * Path to the action view file to render, relative to the /views/actions folder
	 * @var string
	 */
	public $viewPath = null;

	public function __construct(HttpRequest $request, HttpResponse $response) {
		parent::__construct($request, $response);
		$this->response->setContentType('html');
	}

	/**
	 * Set which view to load at render()
	 * @param string $viewPath		Path to the action view file to render, relative to the /views/actions folder
	 */
	public function setView($viewPath) {
		$viewPath	= preg_replace('/action_/', '', $viewPath);
		if (stripos($viewPath, '/') > 0) {
			$this->viewPath	= $viewPath;
		} else {
			$this->viewPath	= "{$this->name}/{$viewPath}";
		}
	}

	/**
	 * Render html from the views folder
	 * @return string			The html
	 */
	public function render() {
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

		$actionHtml	= '';
		if (is_string($this->viewPath)) {
			ob_start();
			require PATH_ACTION . "{$this->viewPath}.html.php";
			$actionHtml	= ob_get_clean();
		}

		ob_start();
		require PATH_LAYOUT . "{$this->layout}.html.php";
		return trim(ob_get_clean());
	}
}