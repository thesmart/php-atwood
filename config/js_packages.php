<?php
/**
 * This file defines which JavaScripts belong to which packages.  Different packages may be required at any time by
 * calling JsStatics::inclHead('package_name').  In developer mode, these will be rendered into the DOM as
 * individual files.  In prod mode, if possible, packages will be minified + compressed into singular files,
 * one per package.
 */

Use \Atwood\lib\fx\statics\JsStatics;

const JS_BASE = 'base';
const JS_TEST = 'test';
const JS_OLD_IE = 'ie';

// hacks for IE < 9
JsStatics::registerPackage(JS_OLD_IE, array(
	'/js/html5.js'
));

JsStatics::registerPackage(JS_BASE, array(
	'/js/jquery-1.7.2',
	'/js/ender/node_modules/underscore/underscore.js',
	'/js/ender/node_modules/backbone/backbone.js',
	'/js/bootstrap/bootstrap-button.js',
	'/js/bootstrap/bootstrap-collapse.js',
	'/js/bootstrap/bootstrap-modal.js',
	'/js/bootstrap/bootstrap-alert.js',
	'/js/bootstrap/bootstrap-carousel.js',
	'/js/bootstrap/bootstrap-tooltip.js'
));

JsStatics::registerPackage(JS_TEST, array(
	'/js/prettify.min.js'
));