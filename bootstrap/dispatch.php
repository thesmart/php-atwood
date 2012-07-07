<?php
/**
 * Dispatch the request by constructing a Controller and invoking its Action
 * @var \Atwood\lib\Atwood $atWood
 * @var \Monolog\Logger $log
 */

use \Atwood\lib\Url;
use \Atwood\lib\fx\Env;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\exception\ApiException;

// invoke the controller, render output, and echo the http response
try {
	$response		= $atWood->dispatch();
	$response->echoHeaders();
	$response->echoBody();
} catch (ApiException $aex) {
	$errResponse	= new HttpResponse();
	$errResponse->setStatus($aex->status);
	if (Env::mode('dev')) {
		$log->addDebugException($aex);
		$errResponse->setBody(nl2br(htmlspecialchars($aex->getMessage() . $aex->getTraceAsString())));
		$errResponse->echoHeaders();
		$errResponse->echoBody();
	}
} catch (\Exception $ex) {
	$log->addErrorException($ex);

	$errResponse	= new HttpResponse();
	$errResponse->setStatus(500);
	if (Env::mode('dev')) {
		$errResponse->setBody(nl2br(htmlspecialchars($ex->getMessage() . $ex->getTraceAsString())));
		$errResponse->echoHeaders();
		$errResponse->echoBody();
	}
}