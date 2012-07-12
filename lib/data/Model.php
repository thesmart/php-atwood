<?php
namespace Atwood\lib\data;

use \Atwood\lib\data\MongoConnections;
use Atwood\lib\fx\exception\ModelException;
use \Atwood\lib\fx\Env;
use \Atwood\lib\data\Stache;
use \dotty\Dotty;
use Monolog\Logger;

/**
 * A base class for all Models in Atwood.
 */
abstract class Model {

	const DB_NAME = 'main';
	const CACHE_INSTANCE = 'model';
	const CACHE_EXPIRES_IN = 600; // 10-minutes

	/**
	 * The log for all Models of this type
	 * @var Logger
	 */
	protected static $log;

	/**
	 * A collection of Stache objects, keyed by unique-index field names
	 * @var array.<string, Stache>
	 */
	protected static $staches;

	/**
	 * The keys in $this->doc that have unique-key constraints in the database.
	 * @var array
	 */
	protected static $uniqueFields = array('_id');

	/**
	 * Primary key from the database
	 * @var \MongoId
	 */
	public $id	= null;

	/**
	* A an array document, suitable json serialization
	* @var array
	*/
	public $doc;

	public function __construct(array $doc = array()) {
		$this->doc		= $doc;
		if (isset($this->doc['_id'])) {
			$this->id	= $this->doc['_id'];
		} else {
			unset($this->doc['_id']);
		}

		static::init();
	}

//	/**
//	 * Save $this->doc to cache
//	 */
//	protected function saveToCache() {
//		if (!$this->id) {
//			// if not in the DB, do not cache
//			return;
//		}
//
//		// cache $this->doc by each unique field
//		// this is done because unique fields are usually the fields accessed most for reads
//		foreach (static::$uniqueFields as $uniqueFieldName) {
//			if (isset($this->doc[$uniqueFieldName])) {
//				$className	= get_called_class();
//				$cacheKey	= "{$className}::{$uniqueFieldName}::{$this->doc[$uniqueFieldName]}";
//				static::$cache->set($cacheKey, $this->doc, static::CACHE_EXPIRES_IN);
//			}
//		}
//	}gmail
//
//	/**
//	 * Construct an instance of this Model from cache
//	 * @static
//	 * @param string $uniqueFieldName		The name a uniquely constrained field (e.g. _id or email_address)
//	 * @param mixed $uniqueFieldValue		A value that can be cast to a string
//	 * @return Model|null			Null if not found in cache
//	 */
//	protected static function readFromCache($uniqueFieldName, $uniqueFieldValue) {
//		$className	= get_called_class();
//		$cacheKey	= "{$className}::{$uniqueFieldName}::{$uniqueFieldValue}";
//		$doc		= static::$cache->get($cacheKey);
//		if (!$doc instanceof static) {
//			return null;
//		}
//		return new static($doc);
//	}

	/**
	 * Init static properties once
	 * @static
	 */
	private static function init() {
		if (static::$staches) {
			// run once
			return;
		}

		foreach (static::$uniqueFields as $fieldName) {
			static::$staches[$fieldName]	= new Stache();
		}
		static::$log	= getLogger(get_called_class());
	}

	/**
	 * The base-line validation routine
	 * @throws \Atwood\lib\fx\exception\ModelException
	 */
	protected final function baseValidate() {
		if (empty($this->doc)) {
			throw new ModelException('$this->doc is empty');
		}
	}

	/**
	 * Validate $this->doc
	 * @abstract
	 * @return string|bool			True if success. Error string if failed.
	 * @throws \Atwood\lib\fx\exception\ModelException
	 */
	public abstract function validate();

	/**
	 * Get the MongoDB object that backs this Descriptor
	 *
	 * @static
	 * @param bool $isSlaveOk		Set false if you need fast consistency.
	 * @return \MongoDB
	 */
	public static function getDb($isSlaveOk = true) {
		return MongoConnections::getDb(static::DB_NAME, $isSlaveOk);
	}

	/**
	 * Get the MongoCollection object that backs this Descriptor
	 *
	 * @static
	 * @param bool $isSlaveOk		Set false if you need fast consistency.
	 * @return \MongoCollection
	 */
	public static function getCol($isSlaveOk = true) {
		return MongoConnections::getCol(static::DB_NAME, get_called_class(), $isSlaveOk);
	}

	/**
	 * Read many models from a MongoCursor
	 *
	 * @static
	 * @param \MongoCursor $cursor		The MongoCursor to read items from
	 * @param int $limit				Optional. The number of items to read.
	 * @return array		An array of Model instances
	 */
	public static function readMany(\MongoCursor $cursor, $limit = 50) {
		$results	= array();
		while ($cursor->hasNext() && (is_null($limit) || count($results) < $limit)) {
			$doc		= $cursor->getNext();
			$model		= new static($doc);
			$results[]	= $model;
		}

		return $results;
	}

	/**
	 * Read one model instance from the database
	 *
	 * @static
	 * @param mixed $value		A value to search for as criterion
	 * @param string $key		The key to search for as criterion
	 * @return Model|null		A Model object on successful read from DB
	 */
	public static function readOne($value, $key = '_id') {
		// turn string into \MongoId
		if ($key === '_id' && !$value instanceof \MongoId) {
			$value	= new \MongoId($value);
		}

		// check DB
		$col	= static::getCol(true);
		$doc	= $col->findOne(array($key => $value));

		if (is_null($doc)) {
			return null;
		}

		$model	= new static($doc);
		return $model;
	}

	/**
	 * Save
	 * @return bool		True if passes validation and insert was attempted
	 * @throw \MongoCursorException
	 * @throw ModelException
	 *
	 */
	public function save() {
		$col = static::getCol(false);

		$this->baseValidate();
		$this->validate();

		$col->save($this->doc, array(
			'safe'	=> true
		));

		if (!empty($this->doc['_id'])) {
			$this->id	= $this->doc['_id'];
		}

		return true;
	}

	/**
	 * Delete this model.
	 */
	public function delete() {
		if (!$this->id) {
			return;
		}

		$col = static::getCol(false);
		$col->remove(array('_id' => $this->id), array(
			'justOne'	=> true
		));

		$this->id	= null;
		$this->doc	= null;
	}
}