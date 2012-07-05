<?php
namespace Smart\lib\fx\exception;

/**
 * Thrown when an API error occurs, for whatever reason.
 */
class ApiException extends \RuntimeException {

	/**
	 * HTTP Status
	 * @var int
	 */
	public $status		= 500;

	/**
	 * Create a Controller/Action exception
	 * @param $userMsg
	 * @param $status
	 */
	public function __construct($userMsg, $status) {
		parent::__construct($userMsg);
		$this->status	= $status;
	}
}