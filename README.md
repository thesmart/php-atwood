# Atwood PHP Framework

Atwood is an MVC framework for PHP 5.3+ and HTML5.

**Do you like any of these?**

 * [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
 * [Backbone.js](http://backbonejs.org/)
 * [CoffeeScript](http://coffeescript.org/)
 * [Less](http://lesscss.org/)
 * [SpriteMapper](http://yostudios.github.com/Spritemapper/)

**... then Atwood is for you.**

Atwood is well documented and comprehensible.  Conventions are easy to understand, and a read-through of code takes about
10-minutes by starting with *php-atwood/public/index.php*.

Check out the [QuickStart Guide](QUICKSTART.md)

# Project Status

Atwood is currently **pre-release** and should not be used in production.

## TODO List

 * unit-tests - passing code coverage of /lib
 * sprites
	* cli script for generating sprited images
	* statics end-point for just-in-time sprited images
 * models
	* create a base class for models that utilize Stache and MongoConnection classes
 * backbone.js
	* get backbone working flawlessly
 * generators
	* build a set of cli generators that create MVC templates
 * video
	* create a "getting started" video

# Features

 * Development
	* HTML5, REST, and CLI support
	* Just-in-time compilation of [.coffee](http://coffeescript.org/) and [.less](http://lesscss.org/) files
	* A complete catalog of element templates that support [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
  * Request routing, similar to Rails
	* Web-based unit-test framework, powered by phpUnit, with code coverage analysis
 * Configuration
	* Unlimited configuration files for different team members, test, and production environments
 * Performance
	* Easy horizontal scaling with [MongoDB](http://www.mongodb.org/)
	* Automatic write-through caching with [Memcached](http://memcached.org/)
	* Scripts for extreme compression and optimization of JS, CSS, and image files
	* Easy JavaScript and CSS package dependency management

# Special Thanks

Thanks to [Jeff Atwood](http://www.codinghorror.com/blog/2012/06/the-php-singularity.html) for the inspiration to create php-atwood.