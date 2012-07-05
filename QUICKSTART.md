# php-atwood Quickstart Guide

Eventually, perhaps there will be an installation script.

### Step 0) Checkout Atwood source

	$ git clone git://github.com/thesmart/php-atwood.git

### Step 1) Configure Apache2

Apache must be configured to:

 1. Serve from the php-atwood root folder
 1. Allow override of settings via php-atwood/.htaccess (i.e. AllowOverride All)

An example configuration file is provided:

	$ cat php-atwood/apache2.conf

You can replace your Apache2 configuration:

	$ sudo rm /etc/apache2/sites-available/*
	$ sudo cp php-atwood/apache2.conf /etc/apache2/sites-available/atwood

Create a symbolic file link that targets php-atwood:

	$ ln -fs php-atwood /var/www/link
	$ chgrp www-pub /var/www/link
	$ chmod 755 /var/www/link

Restart apache

	$ sudo apache2ctl restart

Debug any problems by looking in the log file

	$ tail /var/log/apache2/error.log

### Step 3) Meet operational dependencies

At run-time, Atwood will check for these requirements:

 * Ubuntu apt packages (or equivalent):
  * apache2
  * libapache2-mod-php5
  * mongodb-10gen
  * memcached
  * nodejs
  * npm
 * PHP Packages:
  * php-pear
  * php5-curl
  * php5-mcrypt
  * php5-memcache
  * php5-tidy
  * php5-xmlrpc
  * php5-geoip
  * php5-memcached
 * PECL Packages:
  * apc
  * mongo
  * pear
  * xdebug (development environments only)
 * PEAR Packages:
  * pear.phpunit.de/PHPUnit (development environments only)
  * pear.phpunit.de/PHP_CodeCoverage (development environments only)
 * Node Packages via "npm":
  * less
  * coffee-script
 * Python easy_install packages:
  * spritemapper

Install any missing packages. A detailed walkthrough for Ubuntu 11 setups will come eventually. Message me if you would like some notes.

### Step 4) Set run-time configuration

Atwood has a configuration system that allows you to provide your own api keys, db connection strings, etc.  Each "configuration set" is stand-alone, and located in *php-atwood/config/sets*.

A configuration set is loaded depending on the environment variable "conf". You can change which set is loaded by editing *php-atwood/.htaccess*:

	### Environment variables ###
	SetEnvIfNoCase Host atwood\.com conf=prod mode=prod
	SetEnvIfNoCase Host dev\.vm conf=dev mode=dev

change to

	### Environment variables ###
	SetEnvIfNoCase Host YOUR-PRODUCTION-URL conf=prod mode=prod
	SetEnvIfNoCase Host YOUR-DEV-URL conf=YOUR-SETTINGS-FILE mode=dev

### Step 5) Try running php-atwood

Restart apache

	$ sudo apache2ctl restart

Debug any problems by looking in the log files

	$ tail /var/log/apache2/error.log
	$ tail /var/log/apache2/access.log
