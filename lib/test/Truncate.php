<?php
namespace Smart\lib\test;

use Smart\lib\data\MongoConnections;
use \Smart\lib\fx\Env;
use \Smart\lib\data\Stache;


/**
 * Truncates all data across the application stack
 */
class Truncate {

	public static function all() {
		static::allPersistent();
		static::allCache();
	}

	public static function allPersistent() {
		self::allMongo();
	}

	public static function allMongo() {
		// drop all collections and then recreate them
		foreach (MongoConnections::getAllDbs() as /** @var $db \MongoDb */$db) {
			$cols	= $db->listCollections();
			foreach ($cols as /** @var $col \MongoCollection */$col) {
				$col->drop();
				$col	= $db->selectCollection($col->getName());
			}
		}
	}

	public static function allCache() {
		Stache::truncateAll();
	}

	public static function allMemcached() {
		Stache::truncateMemcached();
	}

	public static function allLocalCache() {
		Stache::truncateLocal();
	}
}