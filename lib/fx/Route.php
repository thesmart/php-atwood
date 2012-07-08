<?php

namespace Atwood\lib\fx;

use Atwood\lib\fx\controllers\Controller;
use Atwood\lib\fx\HttpRequest;
use Atwood\lib\fx\HttpResponse;
use Atwood\lib\fx\controllers\HttpController;
use Atwood\lib\fx\controllers\HtmlController;
use Atwood\lib\fx\controllers\ApiController;

/**
 * Based on Slim by Josh Lockhart <info@slimframework.com>
 */
class Route {
	const CONTROLLER_TYPE_HTML		= 'Atwood\lib\fx\controllers\HtmlController';
	const CONTROLLER_TYPE_API		= 'Atwood\lib\fx\controllers\ApiController';

	/**
	 * Default conditions applied to all route instances
	 * @var array
	 */
	protected static $defaultConditions = array();

	/**
	 * The default action view to use.
	 * @see $this->actionView
	 * @var string
	 */
	protected static $defaultActionView = 'default/blank';

	/**
	 * The default layout view to use
	 * @see $this->layoutView
	 * @var
	 */
	protected static $defaultLayoutView = 'default';

	/**
	 * The route pattern (e.g. "/books/:id")
	 * @var string
	 */
	protected $pattern;

	/**
	 * The route closure
	 * @var \Closure
	 */
	protected $closure;

	/**
	 * Conditions for this route's URL parameters
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * HTTP methods supported by this Route
	 * @var array
	 */
	protected $methods = array('get');

	/**
	 * Key-value array of URL parameters
	 * @var array
	 */
	protected $params = array();

	/**
	 * The full namespace of the controller class extending from Atwood\lib\fx\controllers\HttpController
	 * @var string
	 */
	protected $controllerClass = self::CONTROLLER_TYPE_HTML;

	/**
	 * The Controller method to invoke
	 * @var string
	 */
	protected $controllerMethod = null;

	/**
	 * @var string
	 */
	protected $actionView = null;

	/**
	 * @var string
	 */
	protected $layoutView = null;

	/**
	 * Constructor
	 *
	 * @param   string  $pattern    The URL pattern (e.g. "/books/:id")
	 */
	public function __construct($pattern) {
		$this->setPattern($pattern);
		$this->setConditions(self::$defaultConditions);
		$this->setView(self::$defaultLayoutView);
	}

	/**
	 * Set default route conditions for all instances
	 *
	 * @param   array $defaultConditions
	 * @return  void
	 */
	public static function setDefaultConditions(array $defaultConditions) {
		self::$defaultConditions = $defaultConditions;
	}

	/**
	 * Set default view for all instances
	 * @static
	 * @param $layout
	 */
	public static function setDefaultLayout($layout) {
		self::$defaultLayoutView	= $layout;
	}

	/**
	 * Get route pattern
	 *
	 * @return string
	 */
	public function getPattern() {
		return $this->pattern;
	}

	/**
	 * Set route pattern
	 *
	 * @param   string $pattern
	 * @return  Route
	 */
	public function setPattern($pattern) {
		$this->pattern = str_replace(')', ')?', (string)$pattern);
		return $this;
	}

	/**
	 * Get route Closure
	 *
	 * @return \Closure
	 */
	public function getClosure() {
		return $this->closure;
	}

	/**
	 * Set route Closure
	 *
	 * @param \Closure $closure
	 * @return Route
	 */
	public function setClosure($closure) {
		$this->closure = $closure;
		return $this;
	}

	/**
	 * Get route conditions
	 *
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * Set route conditions
	 *
	 * @param   array $conditions
	 * @return  Route
	 */
	public function setConditions(array $conditions) {
		$this->conditions = $conditions;
		return $this;
	}

	/**
	 * Get route parameters
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * Set route parameters
	 *
	 * @param   array $params
	 * @return  Route
	 */
	public function setParams($params) {
		$this->params = $params;
		return $this;
	}

	/**
	 * Get route parameter value
	 * @param $index
	 * @return mixed
	 * @throws \InvalidArgumentException If route parameter does not exist at index
	 */
	public function getParam($index) {
		if (!isset($this->params[$index])) {
			throw new \InvalidArgumentException('Route parameter does not exist at specified index');
		}
		return $this->params[$index];
	}

	/**
	 * Set route parameter value
	 *
	 * @param   string  $index Name of URL parameter
	 * @param   mixed   $value The new parameter value
	 * @return  Route
	 */
	public function setParam($index, $value) {
		$this->params[$index] = $value;
		return $this;
	}

	/**
	 * Does a param exist?
	 * @param $index
	 * @return bool
	 */
	public function hasParam($index) {
		return isset($this->params[$index]);
	}

	/**
	 * Set the Controller to construct when route is ready for dispatch.
	 * @param string $controllerName		the class name of a controller class extending from Atwood\lib\fx\controllers\HttpController
	 * @param string $actionName			Optional. The action method in the controller to execute.
	 * @return Route
	 */
	public function setController($controllerName, $actionName = null) {
		if ($controllerName === self::CONTROLLER_TYPE_HTML || $controllerName === self::CONTROLLER_TYPE_API) {
			$this->controllerClass	= $controllerName;
		} else {
			$this->controllerClass	= "Atwood\\controllers\\{$controllerName}";
		}

		$this->controllerMethod	= $actionName;
		if ($this->controllerMethod && strpos($this->controllerMethod, 'action_') === false) {
			// add action_ prefix
			$this->controllerMethod	= "action_{$this->controllerMethod}";
		}

		return $this;
	}

	/**
	 * Get the Controller that maps to this route
	 * @throws \RuntimeException
	 *
	 * @param HttpRequest $request
	 * @param HttpResponse $response
	 * @return HttpController		The controller that was dispatched, or
	 */
	public function dispatchController(HttpRequest $request, HttpResponse $response) {
		// check if controller class exists
		$classExists	= @class_exists($this->controllerClass, true);
		if (!$classExists) {
			throw new \RuntimeException(sprintf('Route "%s" specifies invalid HttpController "%s".', $this->pattern, $this->controllerClass));
		}

		// security check on type HttpController
		$controllerRef		= new \ReflectionClass($this->controllerClass);
		if (!$controllerRef->isSubclassOf('\\Atwood\\lib\fx\\controllers\\HttpController')) {
			throw new \RuntimeException(sprintf('Route "%s" specifies class "%s", does not extend HttpController.', $this->pattern, $this->controllerClass));
		}

		/** @var \Atwood\lib\fx\controllers\Controller $controller */
		$controller		= $controllerRef->newInstance($request, $response);

		$methodRef		= null;
		if ($this->controllerMethod) {
			// security check the Action method
			if (!$controllerRef->hasMethod($this->controllerMethod)) {
				throw new \RuntimeException(sprintf('Route "%s" specifies invalid Action "%s" for Controller "%s".', $this->pattern, $this->controllerMethod, $this->controllerClass));
			}

			$methodRef			= new \ReflectionMethod($controller, $this->controllerMethod);
			if (!$methodRef->isPublic()) {
				throw new \RuntimeException(sprintf('Route "%s" specifies non-public Action "%s" for Controller "%s".', $this->pattern, $this->controllerMethod, $this->controllerClass));
			}
		}

		// set parameters derived from the route
		$controller->setData($this->params);

		// set view that was set via route
		if ($controller instanceof HtmlController) {
			/** @var HtmlController $controller */
			$controller->setView($this->actionView);
			$controller->setLayout($this->layoutView);
		}

		// run setup
		$controller->setUp();

		// invoke the controller method if available
		if ($methodRef) {
			$methodRef->invoke($controller);
		}

		if ($this->closure) {
			/** @var \Closure $closure */
			$this->closure->__invoke($controller);
		}

		return $controller;
	}

	/**
	 * Append supported HTTP methods (alias for Route::appendHttpMethods)
	 * @return Route
	 */
	public function via() {
		$args = func_get_args();
		$this->methods = array_merge($this->methods, $args);
		return $this;
	}

	/**
	 * Get supported HTTP methods
	 *
	 * @return array
	 */
	public function getHttpMethods() {
		return $this->methods;
	}

	/**
	 * Detect support for an HTTP method
	 * @param $method
	 * @return bool
	 */
	public function supportsHttpMethod($method) {
		return in_array($method, $this->methods);
	}

	/**
	 * Set the view for a route
	 * @param string $action	The view file to load during dispatch, relative to the /views/actions folder
	 * @param string $layout	Optional. The layout file to load during dispatch, relative to the /views/layouts folder
	 */
	public function setView($action, $layout = null) {
		// prevent folder navigation
		if (empty($layout)) {
			$layout	= self::$defaultLayoutView;
		} else {
			$layout	= str_replace('\\', '/', $layout);
			$layout	= str_replace('.', '', $layout);
		}

		if (empty($action)) {
			$action	= self::$defaultActionView;
		} else {
			$action	= str_replace('\\', '/', $action);
			$action	= str_replace('.', '', $action);
		}

		$this->actionView	= $action;
		$this->layoutView	= $layout;
	}

	/**
	 * Matches URI?
	 *
	 * Parse this route's pattern, and then compare it to an HTTP resource URI
	 * This method was modeled after the techniques demonstrated by Dan Sosedoff at:
	 *
	 * http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/
	 *
	 * @param   string  $resourceUri A Request URI
	 * @return  bool
	 */
	public function matches($resourceUri) {
		//Extract URL params
		preg_match_all('@:([\w]+)|\*@', $this->pattern, $paramNames, PREG_PATTERN_ORDER);
		$paramNames = $paramNames[0];

		// Convert * wildcards into regex patterns
		$wildcards = array_keys($paramNames, '*');
		if (!empty($wildcards)) {
			foreach ($wildcards as $key) {
				$this->pattern = preg_replace('@(?<!\\\\)\*@', '(?P<slim_route_wildcard' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%/]+)', $this->pattern, 1);
				$paramNames[$key] = ':slim_route_wildcard' . $key;
			}
		}

		//Convert URL params into regex patterns, construct a regex for this route
		$patternAsRegex = preg_replace_callback('@:[\w]+@', array($this, 'convertPatternToRegex'), $this->pattern);
		if (substr($this->pattern, -1) === '/') {
			$patternAsRegex = $patternAsRegex . '?';
		}
		$patternAsRegex = '@^' . $patternAsRegex . '$@';

		//Cache URL params' names and values if this route matches the current HTTP request
		if (preg_match($patternAsRegex, $resourceUri, $paramValues)) {
			array_shift($paramValues);
			foreach ($paramNames as $index => $value) {
				$val = substr($value, 1);
				if (isset($paramValues[$val])) {
					if (strpos($val, 'slim_route_wildcard') === 0) {
						$this->params[$val] = explode('/', urldecode($paramValues[$val]));
					} else {
						$this->params[$val] = urldecode($paramValues[$val]);
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Convert a URL parameter (e.g. ":id") into a regular expression
	 *
	 * @param   array $matches   URL parameters
	 * @return  string  Regular expression for URL parameter
	 */
	protected function convertPatternToRegex($matches) {
		$key = str_replace(':', '', $matches[0]);
		if (array_key_exists($key, $this->conditions)) {
			return '(?P<' . $key . '>' . $this->conditions[$key] . ')';
		} else {
			return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
		}
	}

	/**
	 * Merge route conditions
	 *
	 * @param array $conditions Key-value array of URL parameter conditions
	 * @return  Route
	 */
	public function conditions(array $conditions) {
		$this->conditions = array_merge($this->conditions, $conditions);
		return $this;
	}
}