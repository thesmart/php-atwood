<?php
/**
 * Dispatch the request by constructing a Controller and invoking its Action
 * @var \Horde\Routes\Horde_Routes_Mapper $map
 * @var \Monolog\Logger $log
 */

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
use \Atwood\lib\fx\controllers\Controller;
use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\exception\ApiException;

// handle request
$request	= new HttpRequest($_SERVER);
$response	= new HttpResponse();

// handle route
$route	= $map->match($request->url->pathRelative());
if (is_null($route)) {
	$log->debug(sprintf('Path "%s" does not match any route.', $request->url->pathRelative()));
	$response->setStatus(404);
	$response->echoHeaders();
	exit;
}

$controllerName		= $route['controller'];
$controllerName		= preg_replace('/\//', '\\', $controllerName);
$controllerName		= preg_replace('/\./', '', $controllerName);
$controllerName		= "Atwood\\controllers\\{$controllerName}";
$actionName			= 'action_' . $route['action'];

if (isset($_GET['testRoute']) && Env::mode('dev')) {
	var_dump($route);
	exit;
}

// check if controller class exists
$classExists	= @class_exists($controllerName, true);
if (!$classExists) {
	$log->crit(sprintf('Path "%s" specifies invalid Controller "%s".', $request->url->pathRelative(), $controllerName));
	$response->setStatus(404);
	$response->echoHeaders();
	exit;
}

// security check on type Controller
$controllerRef		= new \ReflectionClass($controllerName);
if (!$controllerRef->isSubclassOf('\\Atwood\\lib\fx\\controllers\\Controller')) {
	$log->crit(sprintf('Path "%s" specifies class "%s", does not extend Controller.', $request->url->pathRelative(), $controllerName));
	$response->setStatus(404);
	$response->echoHeaders();
	exit;
}

/** @var \Atwood\lib\fx\controllers\Controller $controller */
$controller			= $controllerRef->newInstance($route, $request, $response);
$controller->setData($route);

// security check on Action method
if (!$controllerRef->hasMethod($actionName)) {
	$log->crit(sprintf('Path "%s" specifies invalid Action "%s" in Controller "%s".', $request->url->pathRelative(), $actionName, $controllerName));
	$response->setStatus(404);
	$response->echoHeaders();
	exit;
}

$methodRef			= new \ReflectionMethod($controller, $actionName);
if (!$methodRef->isPublic()) {
	$log->crit(sprintf('Path "%s" specifies non-pubic Action "%s" in Controller "%s".', $request->url->pathRelative(), $actionName, $controllerName));
	$response->setStatus(404);
	$response->echoHeaders();
	exit;
}

// invoke!
try {
	$methodRef->invoke($controller);
	$response->echoHeaders();
	echo $controller->render();
} catch (ApiException $aex) {
	$response->setStatus($aex->status);
	$response->echoHeaders();

	if (Env::mode('dev')) {
		$log->addDebugException($aex);
		echo $aex->getMessage();
		echo $aex->getTraceAsString();
	}
} catch (Exception $ex) {
	$log->addErrorException($ex);

	$response->setStatus(500);
	$response->echoHeaders();

	if (Env::mode('dev')) {
		echo $ex->getMessage();
		echo $ex->getTraceAsString();
	}
}

