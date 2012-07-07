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
	private $layout = 'blank';

	/**
	 * Path to the action view file to render, relative to the /views/actions folder
	 * @var string
	 */
	private $viewPath = null;

	/**
	 * The id to set the body tag's id attribute
	 * @var string
	 */
	private $bodyId;

	/**
	 * Classes to append to the body tag's class attribute
	 * @var array
	 */
	private $bodyClasses	= array();

	public function __construct(HttpRequest $request, HttpResponse $response) {
		parent::__construct($request, $response);
		$this->response->setContentType('html');
		$this->bodyId	= $this->name;
	}

	/**
	 * Set the Html layout template to load at render()
	 * @param $layout
	 */
	public function setLayout($layout) {
		$this->layout	= $layout;
	}

	/**
	 * Set which view to load at render()
	 * @param string $viewPath		Path to the action view file to render, relative to the /views/actions folder
	 */
	public function setView($viewPath) {
		$viewPath	= preg_replace('/action_/', '', $viewPath);
		$viewPath	= trim($viewPath, '/');

		$this->bodyClasses['view']	= $viewPath;

		if (stripos($viewPath, '/') > 0) {
			$this->viewPath	= $viewPath;
		} else {
			$this->viewPath	= "{$this->name}/{$viewPath}";
		}
	}

	/**
	 * Add a class to the body tag that would be rendered by this HtmlController
	 * @param $class
	 */
	public function addClass($class) {
		$this->bodyClasses[]	= $class;
	}

	/**
	 * Render html from the views folder
	 * @return string			The html
	 */
	public function render() {
		if ($this->response->isRedirected()) {
			// do not render if redirected
			return '';
		}

		extract($this->data);
		$bodyId		= $this->bodyId;
		$bodyClass	= implode(' ', $this->bodyClasses);

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