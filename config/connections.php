<?php
/**
 * This script manages persistent database connections.
 */

namespace Atwood\connections;

use \Atwood\lib\fx\Env;
use \Atwood\lib\data\MongoConnections;
use \Atwood\lib\data\Stache;

ini_set('mongo.cmd', ':');
foreach (Env::get('mongo') as $attributes) {
	MongoConnections::connect($attributes['name'], $attributes['connection_str'], $attributes['is_replica_set']);
}

// setup some instance cache
Stache::setInstance(new Stache());

//foreach (Env::get('memcached.connections') as $name => $count) {
//	$mcId	= mt_rand(1, $count);
//	$mc	= new \Memcached("atwood-{$name}-{$mcId}");
//	$mc->addServer(Env::get("memcache.{$name}.addr"), Env::get("memcache.{$name}.port"));
//	Stache::setInstance(new Stache($mc), $name);
//}
