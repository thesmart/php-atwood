<?php
/**
 * This file defines which CSS files belong to which packages.  Different packages may be required at any time by
 * calling CssStatics::inclHead('package_name').  In developer mode, these will be rendered into the DOM as
 * individual files.  In prod mode, if possible, packages will be minified + compressed into singular files,
 * one per package.
 */

Use \Atwood\lib\fx\statics\CssStatics;

const CSS_BASE = 'base';
const CSS_TEST = 'test';

CssStatics::registerPackage(CSS_TEST, array(
		'/css/prettify.min.css',
		'/css/test.css'
));

CssStatics::registerPackage(CSS_BASE, array(
		'/less/bootstrap.css'
));
