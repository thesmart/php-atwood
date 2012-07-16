<?php
namespace Atwood\models;

use Atwood\lib\sec\Pbkdf2;
use Respect\Validation\Validator;
use \Respect\Validation\Exceptions\ValidationException;

/**
 * Represents a session for a user
 */
class Session extends \Atwood\lib\fx\models\Model implements \Atwood\tests\lib\fx\models\IApiModel {

	/**
	 * @inheritDoc
	 */
	public function toApi() {
		$data	= array(
			'key'			=> $this->doc['key'],
			'ver'			=> $this->doc['ver'],
			'last_visit'	=> $this->doc['last_visit']->sec
		);

		return $data;
	}

	/**
	 * Start a new session
	 * @static
	 * @return Session
	 */
	public static function start() {
		$session	= new Session(array(
			'key'			=> substr(Pbkdf2::generateRandomSalt(), 0, 48),
			'ver'			=> '0.0.1',
			'first_visit'	=> new \MongoDate(),
			'last_visit'	=> new \MongoDate()
		));
		$session->create();
		return $session;
	}

	/**
	 * @static
	 * @param string $key		The secret key of the session
	 * @return Session
	 */
	public static function resume($key) {
		/** @var $session Session */
		$session	= static::readOne($key, 'key');
		if ($session && empty($session->doc['logout'])) {
			// we have a match
			$session->setVisited();
			return $session;
		}

		return null;
	}

	/**
	 * Mark that the session was visited upon again
	 */
	public function setVisited() {
		if (!isset($this->doc['last_visit'])) {
			$this->update(new \MongoDate(), 'last_visit');
			return;
		}

		/** @var $lastVisit \MongoDate */
		$lastVisit = $this->doc['last_visit'];
		if ($lastVisit->sec % 900 === 0) {
			// only update every 15-minutes
			$this->update(new \MongoDate(), 'last_visit');
		}
	}

	/**
	 * Update the session to be logged out, and therefore inaccessible
	 */
	public function setLogout() {
		if (!isset($this->doc['logout'])) {
			$this->update(new \MongoDate(), 'logout');
			return;
		}
	}

	/**
	 * Set an email address for the session
	 * @param string $emailAddress
	 */
	public function setEmail($emailAddress) {
		$v	= Validator::create();
		$emailAddress = mb_strtolower($emailAddress);
		$v->email()->assert($emailAddress);
	}
}