<?php
namespace Atwood\lib\test;

use Atwood\lib\data\MongoConnections;
use Atwood\lib\fx\Env;
use Atwood\lib\data\Stache;
use Atwood\lib\oop\Singleton;


/**
 * Truncates all data across the application stack
 */
class Truncate {

	public static function all() {
		static::allDatabases();
		static::allCaches();
	}

	public static function allDatabases() {
		// drop all collections and then recreate them
		foreach (MongoConnections::getAllDbs() as /** @var $db \MongoDb */$db) {
			$cols	= $db->listCollections();
			foreach ($cols as /** @var $col \MongoCollection */$col) {
				$col->drop();
			}
		}
	}

	public static function allCaches() {
		Stache::truncate();
		Singleton::clearAllInstances();
	}
}