<?php
namespace Atwood\lib\fx\exception;

class Trace {
	protected $path;

	public $trace;
	public $frame;
	public $frameFile;
	public $frameLine;

	public function __construct(\Exception $ex, $path = null) {
		$this->path		= str_replace('\\', '/', $path);
		$this->parse(explode("\n", $ex->getTraceAsString()));
	}

	/**
	 * Return a shorter trace
	 * @return array
	 */
	public function __toString() {
		return implode("\n", $this->trace);
	}

	protected function parse(array $trace) {
		$this->trace		= $trace;
		$this->frame		= null;
		$this->frameFile	= null;
		$this->frameLine	= null;

		foreach ($this->trace as &$frame) {
			$frame	= preg_replace('/^#\d+ /', '', $frame);
			if (stripos($frame, $this->path)) {
				// this path is the active one
				$this->frame		= $frame;

				$matches	= array();
				preg_match('/^(.)+\(([0-9]+)\)/', $frame, $matches);
				$this->frameFile	= $matches[1];
				$this->frameLine	= $matches[2];
			}
		} unset($frame);
	}
}