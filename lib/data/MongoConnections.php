<?php
namespace Atwood\lib\data;

use \Atwood\lib\fx\Env;

/**
 * Creates and manages persistent connections to MongoDB. This enables optimal connections depending on a mix of
 * performance versus consistency.  Some connections are high-speed read, some high-speed write, etc.
 */
class MongoConnections {

	/**
	 * Timeout for connecting to Mongo
	 * @var int
	 */
	const TIMEOUT = 3000;

	protected static $mongo = array();
	protected static $primary = array();
	protected static $secondary = array();
	protected static $namespace = '';

	/**
	 * Connect to a Mongo server, and store a reference.
	 * NOTE: Use one connection per database.
	 *
	 * @static
	 * @param string 	$dbName			The database name
	 * @param string 	$connectionStr	The connection string
	 * @param bool		$isReplicaSet		Whether or not this connection is a replica set
	 * @return \Mongo
	 */
	public static function connect($dbName, $connectionStr, $isReplicaSet = true) {
		// connect
		$mongo = new \Mongo("$connectionStr/$dbName", array(
			'persist' => $dbName,
			'timeout' => self::TIMEOUT,
			'replicaSet' => $isReplicaSet,
			'connect' => true
	    ));

		self::$mongo[$dbName] = $mongo;
		return $mongo;
	}

	/**
	 * Useful for testing, where we want a namespace to delete data by
	 *
	 * @static
	 * @param string $namespace
	 * @return mixed
	 */
	public static function setNamespace($namespace = '') {
		if (!strlen($namespace)) {
			self::$namespace	= '';
			return;
		}

		self::$namespace = $namespace;
	}

	/**
	 * Get a connected Mongo object
	 *
	 * @param string $dbName				The database / connection name
	 * @return \Mongo
	 * @throws \InvalidArgumentException	Thrown if the connection doesnt exist
	 */
	public static function getMongo($dbName) {
		if (!isset(self::$mongo[$dbName])) {
			throw new \InvalidArgumentException(sprintf('Unknown mongo connection "%s"', $dbName));
		}

		return self::$mongo[$dbName];
	}

	/**
	 * Get a reference to a MongoDB object
	 *
	 * @param string $dbName			The database / connection name
	 * @param bool $isSlaveOk			Is this connection ok to wait on slave?
	 * @param string $namespace			The namespace to segment the DB by.
	 * @return \MongoDB
	 *
	 * @static
	 */
	public static function getDb($dbName, $isSlaveOk = false) {
		$realDbName	= $dbName;
		if (strlen(self::$namespace)) {
			$realDbName .= '_' . self::$namespace;
		}

		$dbs = self::$primary;
		if ($isSlaveOk) {
			$dbs = self::$secondary;
		}

		if (isset($dbs[$dbName])) {
			return $dbs[$dbName];
		}

		$mongo		= self::getMongo($dbName);

		$dbP = $mongo->selectDB($realDbName);
		self::$primary[$dbName] = $dbP;

		$dbS = $mongo->selectDB($realDbName);
		$dbS->setSlaveOkay(true);
		self::$secondary[$dbName] = $dbS;
		
		return self::getDb($dbName, $isSlaveOk);
	}

	/**
	 * Get a MongoCollection object
	 *
	 * @static
	 * @param string $dbName
	 * @param string $collectionName
	 * @param bool $isSlaveOk
	 * @return \MongoCollection
	 */
	public static function getCol($dbName, $collectionName, $isSlaveOk = false) {
		$db	= self::getDb($dbName, $isSlaveOk);
		return $db->selectCollection($collectionName);
	}

	/**
	 * Get all connected MongoDB objects
	 *
	 * @static
	 * @return array
	 */
	public static function getAllDbs() {
		$allDbNames	= array_keys(self::$mongo);
		$allDbs		= array();
		foreach ($allDbNames as $dbName) {
			$allDbs[$dbName]	= static::getDb($dbName);
		}

		return $allDbs;
	}

	/**
	 * Test to see if a database exists for a connection
	 *
	 * @static
	 * @param string $dbName				The database / connection name
	 * @return bool
	 */
	public static function dbExists($dbName) {
		try {
			$mongo = MongoConnections::getMongo($dbName);
		} catch (\Exception $ex) {
			return false;
		}

		return true;
	}
}