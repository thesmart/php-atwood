<?php

use \Atwood\lib\fx\Env;
use \Monolog\Logger;

Env::setInstance(new Env('dev', array(
			'url.scheme'		=> isset($_SERVER) && isset($_SERVER['HTTPS']) ? 'https' : 'http', // http or https?
			'url.domain'		=> array('dev', 'vm'), // dev.vm

			'logger.level'		=> Logger::DEBUG,

			'filechange.checkevery' => 1, // 1-seconds

			'stache.enabled'				=> true,
			'stache.static.enabled'			=> true,
			'stache.apc.enabled'			=> true,
			'stache.memcached.enabled'		=> true,

			'memcached.connections'		=> array('main' => 1),
			'memcache.main.port'		=> 11211,
			'memcache.main.addr'		=> '127.0.0.1',

			'mongo' => array(
				array(
					'name' 				=> 'session',
					'connection_str' 	=> 'mongodb://localhost:27017',
					'is_replica_set'	=> false
				),
				array(
					'name' 				=> 'main',
					'connection_str' 	=> 'mongodb://localhost:27017',
					'is_replica_set'	=> false
				),
			),
		)
	)
);