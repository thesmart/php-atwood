<?php
/**
 * This script manages persistent database connections.
 */

namespace Smart\connections;

use \Smart\lib\fx\Env;
use \Smart\lib\data\MongoConnections;
use \Smart\lib\data\Stache;

ini_set('mongo.cmd', ':');
foreach (Env::get('mongo') as $attributes) {
	MongoConnections::connect($attributes['name'], $attributes['connection_str'], $attributes['is_replica_set']);
}

foreach (Env::get('memcached.connections') as $name => $count) {
	$mcId	= mt_rand(1, $count);
	$mc	= new \Memcached("atwood-{$name}-{$mcId}");
	$mc->addServer(Env::get("memcache.{$name}.addr"), Env::get("memcache.{$name}.port"));
	Stache::setInstance(new Stache($mc), $name);
}
