<?php
namespace Atwood\lib\fx\controllers;

use Monolog\Logger;

abstract class Controller {

	/**
	 * Name of this controller
	 * @var string
	 */
	public $name;

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

	public function __construct() {
		$this->name		= explode('\\', get_called_class());
		$this->name		= $this->name[count($this->name) - 1];
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