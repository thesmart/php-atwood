<?php

namespace Atwood\controllers\html;

use \Monolog\Logger;
use \Atwood\lib\fx\Env;
use \Atwood\lib\test\CodeCoverage;
use \Atwood\lib\fx\statics\JsStatics;
use \Atwood\lib\fx\statics\CssStatics;

/**
 * Runs unit tests
 */
class UnitTest extends \Atwood\lib\fx\controllers\HtmlController {

	public function setUp() {
		$this->setLayout('tests');
		CssStatics::inclHead(CSS_TEST);
		JsStatics::inclHead(JS_TEST);
	}

	private function listTests() {
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(PATH_TESTS),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		$unitTests	= array();
		foreach ($iterator as $dirItr) {
			/** @var $dirItr \DirectoryIterator */
			if ($dirItr->isFile()) {
				$path			= $dirItr->getPath() . '/' . $dirItr->getFilename();
				$path			= mb_substr($path, mb_strlen(PATH_TESTS)); // relative path from /tests
				$path			= mb_substr($path, 0, -mb_strlen('.php')); // no .php extension
				$unitTests[]	= $path;
			}
		}
		return $unitTests;
	}

	private function listLibrary() {
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(PATH_LIBRARY),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		$library	= array();
		foreach ($iterator as $dirItr) {
			/** @var $dirItr \DirectoryIterator */
			if ($dirItr->isFile()) {
				$path			= $dirItr->getPath() . '/' . $dirItr->getFilename();
				$path			= mb_substr($path, mb_strlen(PATH_LIBRARY)); // relative path from /lib
				$path			= mb_substr($path, 0, -mb_strlen('.php')); // no .php extension
				$library[]	= $path;
			}
		}
		return $library;
	}

	/**
	 * Get any thrown exceptions
	 * @return array
	 */
	private function getExceptions(array $testFailures) {
		if (empty($testFailures)) {
			return array();
		}

		$exceptions	= array();
		foreach ($testFailures as /** @var \PHPUnit_Framework_TestFailure $fail */ $fail) {
			$ex = $fail->thrownException();
			if ($fail->isFailure() && $ex instanceof \Exception) {
				$exceptions[]	= $ex;
			}
		}
		return $exceptions;
	}

	/**
	 * List all the potential tests that can be run
	 * @return void
	 */
	public function action_listTests() {
		// test list is set automatically, later when UnitTest->render() is called.
	}

	/**
	 * Run a specific test using PHPUnit
	 * @return void
	 */
	public function action_runTest() {
		// build a class namespace using the requested path
		$unitTest	= array();
		foreach ($this->data as $k => $v) {
			if (isset($v) && mb_substr($k, 0, 2) === 'ns') {
				$pieces	= explode('_', $k);
				$unitTest[(int)$pieces[1]]	= $v;
			}
		}
		$filePath	= PATH_TESTS . implode('/', $unitTest) . '.php';
		$shortName	= implode('\\', $unitTest);
		$unitTest	= '\\Atwood\\tests\\' . $shortName;

		// load the test class
		if (!@file_exists($filePath)) {
			$this->setView('unknownTest');
			return;
		}
		include_once $filePath;
		$class	= new \ReflectionClass($unitTest);

		// detect if class is a valid test
		if (!$class->isSubclassOf('\\Atwood\\lib\\test\\AtwoodTest')) {
			$this->setView('unknownTest');
			return;
		}

		$suite	= new \PHPUnit_Framework_TestSuite($class);

		// prepare the results
		$that	= $this;
		$result	= new \PHPUnit_Framework_TestResult();

		// run the test
		if (Env::mode('dev')) {
			xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
		}

		$suite->run($result);

		if (Env::mode('dev')) {
			$codeCoverage	= new CodeCoverage($shortName, xdebug_get_code_coverage());
			xdebug_stop_code_coverage();
		} else {
			$codeCoverage = null;
		}

		$that->setData(array(
			'suiteName'		=> $shortName,
			'codeCoverage' 	=> $codeCoverage,
			'passed'		=> $result->passed(),
			'skipped'		=> $result->skipped(),
			'notImplemented'	=> $result->notImplemented(),
			'errors'		=> $result->errors(),
			'failures'		=> $result->failures(),
			'numTests'		=> $result->count()
		));
	}

	public function render() {
		$this->setData(array(
			'libs'		=> $this->listLibrary(),
			'unitTests' => $this->listTests()
		));
		return parent::render();
	}
}