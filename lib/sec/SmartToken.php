<?php
namespace Smart\lib\sec;

use \Smart\lib\sec\Pbkdf2;

/**
 * A class the signs arbitrary data securely, and in a nicely URL-transparent way.
 *
 * http://www.ietf.org/rfc/rfc1738.txt - Dec 1994
 * http://www.ietf.org/rfc/rfc3986.txt - Jan 2005
 */
class SmartToken {

	/**
	 * WARNING: NEVER divulge
	 */
	const SHARED_SECRET		= 'spy2no8phu0phuk8whi4rhu2rhym6sych2ku7rhyh9shik5wop';

	/**
	 * A timeout for SmartTokens
	 */
	const TIMEOUT			= 86400; // 24-hours

	/**
	 * Values >5000 improves security, but are more expensive
	 */
	const HASH_ITERATIONS	= 1000;

	public $rawData = null;

	public $encData = null;

	public $time = null;

	public $sig = null;

	protected function __construct() {
	}

	/**
	 * Is this smart token expired?
	 *
	 * @param int $timeout		Optional.  The time-to-timeout
	 * @return bool
	 */
	public function isExpired($timeout = SmartToken::TIMEOUT) {
		return time() >= ($this->time + $timeout);
	}

	/**
	 * Sign the SmartToken
	 *
	 * @param string $secret		Optional. The secret to use during signing.
	 * @return string
	 */
	public function sign($secret = SmartToken::SHARED_SECRET) {
		$baseStr	= implode('|', array($this->time, $this->sig, $this->encData));
		$this->sig	= Pbkdf2::hash($baseStr, $secret, self::HASH_ITERATIONS);
		return $this->sig;
	}

	/**
	 * Decode a token and return the contained data.
	 *
	 * @static
	 * @param string $tokenStr		The string representation of SmartToken
	 * @param string $secret		The secret to use during signing
	 *
	 * @return mixed
	 */
	public static function decodeData($tokenStr, $secret = SmartToken::SHARED_SECRET) {
		$token	= self::decode($tokenStr, $secret);
		if ($token) {
			return $token->rawData;
		}

		return null;
	}

	/**
	 * Decode a token into a SmartToken object.
	 *
	 * @static
	 * @param string $tokenStr		The string representation of SmartToken
	 * @param string $secret		The secret to use during signing
	 *
	 * @return SmartToken			The SmartToken, or null if unable to decode the token
	 */
	public static function decode($tokenStr, $secret = SmartToken::SHARED_SECRET) {
		$token	= new SmartToken();

		$pieces	= explode('-', $tokenStr, 3);
		if (count($pieces) !== 3) {
			return null;
		}

		$token->time	= $pieces[0];
		$token->encData	= $pieces[2]; // the rest of the token

		if ($token->isExpired()) {
			// token has expired
			return null;
		} else if ($token->sign($secret) !== $pieces[1]) {
			// signature match has failed
//			throw new \UnexpectedValueException(sprintf('SmartToken failed signature check: "%s" vrs "%s"', $token->__toString(), $token));
			return null;
		}

		// decode the data
		$data	= @base64_decode(strtr($token->encData, '-_.', '+/='));
		if ($data) {
			$token->rawData	= $data;
		}

		return $token;
	}

	/**
	 * Encode data into a SmartToken
	 *
	 * @static
	 * @param mixed $data		Any data that can be json encoded
	 * @param string $secret	Optional. The secret to use during signing
	 *
	 * @return SmartToken		The SmartToken, or null if unable to encode the data
	 */
	public static function encode($data, $secret = SmartToken::SHARED_SECRET) {
		$token	= new SmartToken();
		$token->time	= time();
		$token->rawData	= $data;

		if (is_scalar($data)) {
			$token->encData	= strval($data);
		} else {
			$token->encData	= json_encode($data);
		}

		// compress & encode
		$token->encData	= @strtr(base64_encode($token->encData), '+/=', '-_.');
		if ($token->encData === false) {
			return null;
		}

		// sign
		$token->sign($secret);
		return $token;
	}

	/**
	 * Output the token as a string
	 * @return string
	 */
	public function __toString() {
		return implode('-', array(
			$this->time,
			$this->sig,
			$this->encData
		));
	}
}
