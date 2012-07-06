<?php
namespace Atwood\lib\fx\controllers;

use Monolog\Logger;

abstract class Controller {

	/**
	 * Information about the current route picked
	 * @var array
	 */
	public $route;

	/**
	 * The logging instance
	 * @var
	 */
	public $log;

	/**
	 * Data that will be passed up to the view
	 * @var array
	 */
	public $data	= array();

	public function __construct(array $route) {
		$this->route	= $route;
		$this->log		= getLogger(get_called_class());
		$this->setUp();
	}

	/**
	 * Setup the controller's defaults
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * Set a data item. This will be passed as a variable name down to the View
	 * @param array $data		Merge this data into already set data
	 * @return array
	 */
	public function setData(array $data) {
		foreach ($data as $k => $v) {
			$this->data[$k]	= $v;
		}
		return $this->data;
	}

	/**
	 * Render the output representation of this Controller
	 * @return string				Html, JSON, or some other output format.
	 */
	public function render() {
		return '';
	}
}