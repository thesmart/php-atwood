<?php
/**
 * The core Atwood dispatch class.
 */

namespace Atwood\lib;

use Horde\Routes\Horde_Routes_Mapper;
use Monolog\Logger;

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
use Atwood\lib\fx\Route;
use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\exception\ApiException;
use Atwood\lib\fx\controllers\Controller;
use Atwood\lib\fx\controllers\HtmlController;


class Atwood {

	/**
	 * The master log for Atwood
	 * @var Monolog\Logger $log
	 */
	private $log;

	/**
	 * The request sent by the client
	 * @var \Atwood\lib\fx\HttpRequest
	 */
	private $request;

	/**
	 * The response to return to the client at the end of the current request process
	 * @var \Atwood\lib\fx\HttpResponse
	 */
	private $response;

	/**
	 * Collection or Routes
	 * @var array
	 */
	private $routes = array();

	/**
	 * Initialize the route mapper
	 */
	public function __construct($server = null) {
		$this->log			= getLogger(get_called_class());
		$this->request		= new HttpRequest($server ? $server : $_SERVER);
		$this->response		= new HttpResponse();
	}

	/**
	 * @param string $pattern
	 * @param string $controllerName		the class name of a controller class extending from Atwood\lib\fx\controllers\HttpController
	 * @param string $actionName			Optional. The action method in the controller to execute.
	 * @return fx\Route
	 */
	public function map($pattern, $controllerName, $actionName = null) {
		$route = new Route($pattern);
		$route->setController($controllerName, $actionName);
		$route->setView($actionName);
		$this->routes[]	= $route;
		return $route;
	}

	/**
	 * Map an HtmlController to a path
	 * @param string $pattern
	 * @return fx\Route
	 */
	public function mapHtml($pattern) {
		return $this->map($pattern, Route::CONTROLLER_TYPE_HTML);
	}

	/**
	 * Map an ApiController to a path
	 * @param string $pattern
	 * @return fx\Route
	 */
	public function mapApi($pattern) {
		return $this->map($pattern, Route::CONTROLLER_TYPE_API);
	}

	/**
	 * Map an ApiController to a path
	 * @param string $pattern
	 * @return fx\Route
	 */
	public function mapClosure($pattern) {
		$route = new Route($pattern);
		$this->routes[]	= $route;
		return $route;
	}

	/**
	 * Dispatch the request by constructing a Controller and invoking its Action
	 */
	public function dispatch() {
		/** @var Route $matchedRoute */
		$matchedRoute	= null;

		$path			= $this->request->url->pathRelative();
		foreach ($this->routes as /** @var Route $route */ $route) {
			if ($route->matches($path)) {
				$matchedRoute	= $route;
				break;
			}
		}

		// handle route
		if (is_null($matchedRoute)) {
			$this->log->debug(sprintf('Path "%s" does not match any route.', $path));
			$this->response->setStatus(404);
			return $this->response;
		}

		// dispatch the controller
		$controller	= $matchedRoute->dispatchController($this->request, $this->response);

		// render
		$this->response->setBody($controller->render());

		return $this->response;
	}

	/**
	 * Set default route conditions for all instances
	 *
	 * @param   array $defaultConditions
	 * @return  void
	 */
	public static function setDefaultConditions(array $defaultConditions) {
		Route::setDefaultConditions($defaultConditions);
	}

	/**
	 * Set default layout for all instances
	 * @static
	 * @param $layout
	 */
	public static function setDefaultLayout($layout) {
		Route::setDefaultLayout($layout);
	}
}