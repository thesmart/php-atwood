<?php

namespace Atwood\controllers;

use \Atwood\lib\fx\HttpRequest;
use \Atwood\lib\fx\HttpResponse;
use \Atwood\lib\fx\exception\ApiException;
use \Monolog\Logger;
use \Atwood\lib\fx\Env;
use \Atwood\lib\fx\filesys\FileChange;
use \Less\lessc;

/**
 * Serves dynamically generated static files
 */
class Statics extends \Atwood\lib\fx\controllers\Controller {

	const TIMEOUT = 60; // 1 min

	/**
	 * @var \Atwood\lib\fx\HttpRequest
	 */
	public $request;

	/**
	 * @var \Atwood\lib\fx\HttpResponse
	 */
	public $response;

	public $output = '';

	public function __construct(array $route, HttpRequest $request, HttpResponse $response) {
		parent::__construct($route);

		$this->request	= $request;
		$this->response	= $response;
	}

	public function setup() {
		if (Env::mode('prod')) {
			$this->response->setCache(604800); // 1-week
		}
	}

	/**
	 * @param $srcExtension
	 * @param $targetExtension
	 * @return string
	 * @throws \Atwood\lib\fx\exception\ApiException
	 */
	private function handleFilePath($srcExtension, $targetExtension) {
		if (preg_match(sprintf('/^[a-zA-Z0-9_\-]+\.%s$/', $targetExtension), $this->data['fileName']) === 0) {
			throw new ApiException('File name rejected.', 405);
		}

		$path	= substr($this->data['fileName'], 0, -strlen(".{$targetExtension}"));
		return $path;
	}

	/**
	 * Check for valid output in cache.
	 * @param $extension
	 * @param $folderPath
	 * @return bool|mixed
	 */
	private function checkCache($extension, $folderPath) {
		$cacheKey		= "Statics::{$extension}";
		if (!FileChange::isFolderChanged($folderPath)) {
			$output	= apc_fetch($cacheKey, $isCache);
			if ($isCache) {
				return $output;
			}
		}

		return false;
	}

	/**
	 * Set the cached output
	 * @param $extension
	 * @param $output
	 */
	private function setCache($extension, $output) {
		$cacheKey		= "Statics::{$extension}";
		apc_store($cacheKey, $output, 604800); // 1-week
	}

	/**
	 * Run CoffeeScript NODE compiler and generate JS
	 * @return void
	 */
	public function action_coffee() {
		$this->response->setContentType('js');

		$path	= $this->handleFilePath('coffee', 'js');
		$path	= pathinfo($path);

		$folderPath	= PATH_STATIC . "coffee/{$path[PATHINFO_FILENAME]}";

		$this->output	= $this->checkCache('coffee', $folderPath);
		if ($this->output) {
			return;
		}

		// make sure we have source to compile
		if (!file_exists($folderPath)) {
			throw new ApiException('File not found.', 404);
		}

		// compile
		$this->output	= shell_exec("coffee --compile --print {$folderPath}");

		$this->setCache('coffee', $this->output);
	}

	/**
	 * Run LESS and generate a CSS file
	 * @return void
	 */
	public function action_less() {
		$this->response->setContentType('css');

		$path	= $this->handleFilePath('coffee', 'css');
		$path	= pathinfo($path);

		$folderPath	= PATH_STATIC . "less/{$path['filename']}";
		$filePath	= "{$folderPath}/{$path['filename']}.less";

		$this->output	= $this->checkCache('less', $folderPath);
		if ($this->output) {
			return;
		}

		// make sure we have source to compile
		if (!file_exists($folderPath)) {
			throw new ApiException('File not found.', 404);
		}

		// compile
		$this->output	= shell_exec("lessc {$filePath}");

		$this->setCache('less', $this->output);
	}

	/**
	 * Render the saved output
	 * @return string
	 */
	public function render() {
		return $this->output;
	}
}