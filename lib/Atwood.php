<?php
/**
 * The core Atwood dispatch class.
 */

namespace Atwood\lib;

use Horde\Routes\Horde_Routes_Mapper;
use Monolog\Logger;

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
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
	 * The route mapper for GET method
	 * @var Horde_Routes_Mapper
	 */
	private $getMapper;

	/**
	 * A route mapper for POST method
	 * @var Horde_Routes_Mapper
	 */
	private $postMapper;

	/**
	 * A route mapper for PUT method
	 * @var Horde_Routes_Mapper
	 */
	private $putMapper;

	/**
	 * A route mapper for DELETE method
	 * @var Horde_Routes_Mapper
	 */
	private $deleteMapper;


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
	 * The next index to assign to a route
	 * @var int
	 */
	private $index = 0;

	/**
	 * All the registered actions
	 * @var array
	 */
	private $actions = array();

	/**
	 * Initialize the route mapper
	 */
	public function __construct($server = null) {
		$map = new Horde_Routes_Mapper();
		// Turn on sub-domain support
		$map->subDomains	= true;
		// Set the environment
		$map->environ		= $server ? $server : $_SERVER;
		// Set the director to scan for controller classes
		$map->directory		= PATH_ROOT . 'controllers';

		$this->log			= getLogger(get_called_class());
		$this->getMapper	= $map;
		$this->request		= new HttpRequest($server ? $server : $_SERVER);
		$this->response		= new HttpResponse();
	}

	/**
	 * Add a GET route that will render HTML to the output buffer
	 * @param string $route				The route to match
	 * @param mixed $action				Either a closure to invoke, or an array(<controllerName>, <actionMethodName>)
	 * @param string $actionView		Optional. The /views/action file to render (without extensions)
	 * @param string $layoutView		Optional. The /views/layout file to render (without extensions)
	 */
	public function getHtml($route, $action, $actionView = null, $layoutView = 'default') {
		if (empty($actionView)) {
			if (is_array($action)) {
				$actionView	= "{$action[0]}/{$action[1]}";
			} else {
				$actionView	= 'default/blank';
			}
		}
		$this->actions[$this->index]	= $action;
		$this->getMapper->connect($route, array(
			'id'			=> $this->index,
			'type'			=> 'html',
			'actionView'	=> $actionView,
			'layoutView'	=> $layoutView
		));
		++$this->index;
	}

	/**
	 * Dispatch the request by constructing a Controller and invoking its Action
	 */
	public function dispatch() {
		// match a route to the path
		$route	= null;
		$path	= $this->request->url->pathRelative();
		if ($this->request->method === HttpRequest::METHOD_GET) {
			$route	= $this->getMapper->match($path);
		} else if ($this->request->method === HttpRequest::METHOD_POST) {
			$route	= $this->postMapper->match($path);
		} else if ($this->request->method === HttpRequest::METHOD_PUT) {
			$route	= $this->putMapper->match($path);
		} else if ($this->request->method === HttpRequest::METHOD_DELETE) {
			$route	= $this->deleteMapper->match($path);
		}

		// handle route
		if (is_null($route)) {
			$this->log->debug(sprintf('Path "%s" does not match any route.', $path));
			$this->response->setStatus(404);
			return $this->response;
		}

		// dispatch the controller
		$action		= $this->actions[$route['id']];
		$controller	= null;
		if (is_array($action)) {
			$controller	= $this->dispatchReflection($action, $route);
		} else if ($action instanceof \Closure) {
			$controller	= $this->dispatchClosure($action, $route);
		}

		// render
		$this->response->setBody($controller->render());

		return $this->response;
	}

	/**
	 * Dispatch a controller that is defined by a callback array, mapping to a concrete class that extends from
	 * \Atwood\lib\fx\controllers\Controller
	 *
	 * @param array $action
	 * @param array $route
	 * @return Controller
	 */
	private function dispatchReflection(array $action, array $route) {
		list($controllerName, $actionName)	= $action;
		$path	= $this->request->url->pathRelative();

		// check if controller class exists
		$classExists	= @class_exists($controllerName, true);
		if (!$classExists) {
			$this->log->crit(sprintf('Path "%s" specifies invalid HttpController "%s".', $path, $controllerName));
			$this->response->setStatus(404);
			return null;
		}

		// security check on type HttpController
		$controllerRef		= new \ReflectionClass($controllerName);
		if (!$controllerRef->isSubclassOf('\\Atwood\\lib\fx\\controllers\\HttpController')) {
			$this->log->crit(sprintf('Path "%s" specifies class "%s", does not extend HttpController.', $path, $controllerName));
			$this->response->setStatus(404);
			return null;
		}

		/** @var \Atwood\lib\fx\controllers\Controller $controller */
		$controller			= $controllerRef->newInstance($this->request, $this->response);

		// security check on Action method
		$methodName	= "action_{$actionName}";
		if (!$controllerRef->hasMethod($methodName)) {
			$this->log->crit(sprintf('Path "%s" specifies invalid Action "%s" in Controller "%s".', $path, $actionName, $controllerName));
			$this->response->setStatus(404);
			return null;
		}

		$methodRef			= new \ReflectionMethod($controller, $methodName);
		if (!$methodRef->isPublic()) {
			$this->log->crit(sprintf('Path "%s" specifies non-pubic Action "%s" in Controller "%s".', $path, $actionName, $controllerName));
			$this->response->setStatus(404);
			return null;
		}

		$this->initController($controller, $route);
		$methodRef->invoke($controller);
		return $controller;
	}

	/**
	 * Dispatch a controller that is defined by an anonymous function Closure
	 *
	 * @param callable $closure
	 * @param array $route
	 * @return fx\controllers\HtmlController
	 */
	private function dispatchClosure(\Closure $closure, array $route) {
		$controller	= new HtmlController($this->request, $this->response);
		$this->initController($controller, $route);
		$closure($controller);
		return $controller;
	}

	/**
	 * After a controller is constructed, it needs to be initialized to a state known by Atwood
	 * @param fx\controllers\Controller $controller
	 * @param array $route
	 */
	private function initController(Controller $controller, array $route) {
		if ($route['type'] === 'html') {
			/** @var HtmlController $controller */

			// prevent folder navigation
			$route['layoutView']	= str_replace('\\', '/', $route['layoutView']);
			$route['layoutView']	= str_replace('.', '', $route['layoutView']);

			if (is_null($route['actionView'])) {
				$route['actionView']	= 'default/blank';
			}

			$route['actionView']	= str_replace('\\', '/', $route['actionView']);
			$route['actionView']	= str_replace('.', '', $route['actionView']);

			$controller->setView($route['actionView']);
			$controller->setLayout($route['layoutView']);
		}
	}

}