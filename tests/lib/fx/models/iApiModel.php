<?php

namespace Atwood\tests\lib\fx\models;

/**
 * This interface defines a contract that a Model must provide a more concise json output, safe for public consumption.
 */
interface IApiModel {

	/**
	 * Output a sub-set of the model, suitable for output to a public API
	 * @abstract
	 * @return array
	 */
	function toApi();
}