<?php
namespace Atwood\lib\data;

use \Atwood\lib\data\MongoConnections;
use \Atwood\lib\fx\exception\ModelException;
use \Atwood\lib\fx\Env;
use \Atwood\lib\data\Stache;
use \dotty\Dotty;
use \Monolog\Logger;

/**
 * A base class for all Models in Atwood.
 */
abstract class Model {

	const DB_NAME = 'main';
	const CACHE_INSTANCE = 'model';
	const CACHE_EXPIRES_IN = 600; // 10-minutes

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
	 * @param string $key		The key to search for as criterion (could be via dot-notation)
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