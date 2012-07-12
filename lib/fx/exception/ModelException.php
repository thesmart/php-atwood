<?php
namespace Atwood\lib\fx\exception;

/**
 * Thrown when an API error occurs, for whatever reason.
 */
class ModelException extends \RuntimeException {
	/**
	 * Create a Controller/Action exception
	 * @param $msg
	 */
	public function __construct($msg) {
		parent::__construct($msg);
	}
}