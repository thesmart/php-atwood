<?php

namespace Atwood\lib\test;

/**
 * Utility class for analysing code coverage of a unit test
 */
class CodeCoverage {

	const CODE_COVERED	= 1;
	const CODE_UNUSED	= -1;
	const CODE_DEAD		= -2;

	protected $pos	= 0;

	protected $countCovered	= 0;
	protected $countUnused		= 0;
	protected $countDead		= 0;
	protected $countTotal		= 0;
	protected $lines			= array();
	protected $linesStatus		= array();

	public function __construct($testPath, array $codeCoverage) {
		$partialPath	= str_replace('\\', '/', substr($testPath, 0, -strlen('Test'))) . '.php';
		$this->parse($partialPath, $codeCoverage);
	}

	/**
	 * Parse the code coverage report
	 * @throws \RuntimeException
	 * @param $partialPath
	 * @param array $codeCoverage
	 * @return void
	 */
	protected function parse($partialPath, array $codeCoverage) {
		$fileToParse	= null;
		$reportToParse	= null;
		foreach ($codeCoverage as $currPath => $report) {
			if (stripos($currPath, $partialPath)) {
				$fileToParse	= $currPath;
				$reportToParse	= $report;
				break;
			}
		}

		if (!isset($reportToParse)) {
			return;
		}

		$this->parseReport($fileToParse, $reportToParse);
	}

	/**
	 * Parse a a specific file in the code coverage report
	 * @param $filePath
	 * @param array $report
	 * @return void
	 */
	protected function parseReport($filePath, array $report) {
		$code				= file_get_contents($filePath);
		$this->lines		= explode(PHP_EOL, $code);
		$this->countTotal	= count($this->lines);
		$this->linesStatus	= $report;

		foreach ($report as $lineNum => $status) {
			if ($status === self::CODE_COVERED) {
				++$this->countCovered;
			} else if ($status === self::CODE_DEAD) {
				++$this->countDead;
			} else if ($status === self::CODE_UNUSED) {
				++$this->countUnused;
			}
		}
	}

	/**
	 * Get a line of code
	 * @param int $lineNum
	 * @return string
	 */
	public function getLine($lineNum) {
		return isset($this->lines[$lineNum]) ? $this->lines[$lineNum] : '';
	}

	/**
	 * Is a line covered or dead?
	 * @param int $lineNum
	 * @return bool
	 */
	public function isCovered($lineNum) {
		$lineNum	+= 1;
		if (isset($this->linesStatus[$lineNum])) {
			return $this->linesStatus[$lineNum] === self::CODE_COVERED;
		}
		return false;
	}

	/**
	 * Is a particular line "dead code", i.e. not reachable in code
	 * @param int $lineNum
	 * @return bool
	 */
	public function isDead($lineNum) {
		$lineNum	+= 1;
		if (isset($this->linesStatus[$lineNum])) {
			return $this->linesStatus[$lineNum] === self::CODE_DEAD;
		}
		return false;
	}

	/**
	 * Is a particular line not covered?
	 * @param int $lineNum
	 * @return bool
	 */
	public function isUncovered($lineNum) {
		$lineNum	+= 1;
		if (isset($this->linesStatus[$lineNum])) {
			return $this->linesStatus[$lineNum] === self::CODE_UNUSED;
		}
		return false;
	}

	/**
	 * Count the total number of lines of source
	 * @return int
	 */
	public function countLines() {
		return $this->countTotal;
	}

	/**
	 * Get the number of lines covered
	 * @return int
	 */
	public function countCoverage() {
		return $this->countCovered;
	}

	/**
	 * Get the number of lines uncovered
	 * @return int
	 */
	public function countUncovered() {
		return $this->countUnused;
	}

	/**
	 * Get the percentage of code covered
	 * @return int
	 */
	public function percentCovered() {
		if ($this->countTotal) {
//			return (int)round((($this->countCovered + $this->countDead) / $this->countTotal) * 100);
			return (int)round(($this->countCovered / ($this->countCovered + $this->countUnused)) * 100);
		} else {
			return 0;
		}
	}
}