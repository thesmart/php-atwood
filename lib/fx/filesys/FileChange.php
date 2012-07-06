<?php
namespace Atwood\lib\fx\filesys;

use \Atwood\lib\fx\Env;

/**
 * A class that aids with detecting file changes in a folder.
 */
class FileChange {

	const CACHE_NS	= 'FileChange';

	/**
	 * Check to see if the contents of a folder has changed
	 * @static
	 * @param string $folderPath
	 * @return bool
	 */
	public static function isFolderChanged($folderPath) {
		if (!self::isTimeToCheck($folderPath)) {
			// not yet time to check again
			return false;
		}

		// remember when we last checked
		self::setLastCheck($folderPath, time());

		$lastHash	= self::getLastChangeHash($folderPath);
		$curHash	= self::getFolderChangeHash($folderPath);

		// remember the last change hash
		if ($lastHash !== $curHash) {
			self::setLastChangeHash($folderPath, $curHash);
			return true;
		}

		return false;
	}

	/**
	 * Get a hash of the last time a file was changed in a folder
	 * @param string $folderPath
	 * @return string
	 */
	private static function getFolderChangeHash($folderPath) {
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($folderPath),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		$modTimes	= array();
		foreach ($iterator as $dirItr) {
			/** @var $dirItr \DirectoryIterator */
			if ($dirItr->isFile()) {
				$modTimes[]	= $dirItr->getMTime();
			}
		}

		return md5(implode('', $modTimes));
	}

	/**
	 * Determine if it is time to check for file changes in a folder
	 * @static
	 * @param string $path
	 * @return bool
	 */
	private static function isTimeToCheck($path) {
		$lastTime	= self::getLastCheckTime($path);
		return time() > $lastTime + Env::get('filechange.checkevery');
	}

	/**
	 * Get the last known change hash
	 * @param string $path
	 * @return string
	 */
	private static function getLastChangeHash($path) {
		$cacheKey	= self::CACHE_NS . '::lastChangeHash::' . $path;
		$changeHash	= apc_fetch($cacheKey, $isCache);
		if ($isCache) {
			// only use caching if in prod mode
			return $changeHash;
		}

		return 'unknown';
	}

	/**
	 * Set the last known change hash
	 *
	 * @static
	 * @param string $path
	 * @param string $changeHash
	 * @return void
	 */
	private static function setLastChangeHash($path, $changeHash) {
		$cacheKey	= self::CACHE_NS . '::lastChangeHash::' . $path;
		apc_store($cacheKey, $changeHash, 6000); // 100-minutes
	}

	/**
	 * @param string $path
	 * @return int
	 */
	private static function getLastCheckTime($path) {
		$cacheKey	= self::CACHE_NS . '::lastCheckTime::' . $path;
		$time		= apc_fetch($cacheKey, $isCache);
		if ($isCache) {
			// only use caching if in prod mode
			return $time;
		}

		return 0;
	}

	/**
	 * @static
	 * @param string $path
	 * @param int $time
	 * @return void
	 */
	private static function setLastCheck($path, $time) {
		$cacheKey	= self::CACHE_NS . '::lastCheckTime::' . $path;
		apc_store($cacheKey, $time, 6000); // 100-minutes
	}
}