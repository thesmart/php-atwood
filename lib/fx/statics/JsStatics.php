<?php

namespace Atwood\lib\fx\statics;

/**
 * A class tracking Javascript dependencies and rendering that markup
 */
class JsStatics {

	/**
	 * @var array
	 */
	public static $packages = array();

	/**
	 * @var array
	 */
	public static $scriptsHead	= array();

	/**
	 * @var array
	 */
	public static $scriptsBody	= array();

	/**
	 * @var array
	 */
	public static $scriptsDomReady	= array();

	/**
	 * Register a package as available
	 *
	 * @static
	 * @param string $name				A name for the package
	 * @param array $scriptPaths		An array of paths references script files
	 */
	public static function registerPackage($name, $scriptPaths) {
		if (isset(static::$packages[$name])) {
			// merge
			static::$packages[$name]	= array_merge(static::$packages[$name], $scriptPaths);
		} else {
			static::$packages[$name]	= $scriptPaths;
		}
	}

	/**
	 * Include a script in the </head> tag
	 * @param array|string $scriptPackOrPath
	 * @return void
	 */
	public static function inclHead($scriptPackOrPath) {
		static::$scriptsHead[]	= $scriptPackOrPath;
	}
	
	/**
	 * Include a script at the opening </body> tag
	 * @param array|string $scriptPackOrPath
	 * @return void
	 */
	public static function inclBody($scriptPackOrPath) {
		static::$scriptsBody[]	= $scriptPackOrPath;
	}

	/**
	 * Include a script after dom is loaded and ready
	 * @param array|string $scriptPackOrPath
	 * @return void
	 */
	public static function inclDomReady($scriptPackOrPath) {
		static::$scriptsDomReady[]	= $scriptPackOrPath;
	}

	public static function renderHead() {
		return static::render(static::$scriptsHead);
	}

	public static function renderBody() {
		return static::render(static::$scriptsBody);
	}

	public static function renderDomReady() {
		return static::render(static::$scriptsDomReady);
	}

	/**
	 * @return string
	 */
	private static function render(array& $scripts) {
		$buff	= array();

		foreach ($scripts as $scriptPackOrPath) {
			if (stripos($scriptPackOrPath, '<script') === 0) {
				// a script tag
				$buff[]	= $scriptPackOrPath;
			} else if ($scriptPackOrPath === JS_OLD_IE) {
				foreach (static::$packages[$scriptPackOrPath] as $src) {
					$buff[] = <<<SCRIPT_FOR_IE
<!--[if lt IE 9]>
	<script src="$src"></script>
<![endif]-->
SCRIPT_FOR_IE;
				}
			} else if (array_key_exists($scriptPackOrPath, static::$packages)) {
				// a package of scripts
				foreach (static::$packages[$scriptPackOrPath] as $src) {
					$buff[] = <<<SCRIPT
<script src="$src"></script>
SCRIPT;
				}
			} else if (filter_var($scriptPackOrPath, FILTER_VALIDATE_URL)) {
					$buff[] = <<<SCRIPT
<script src="$scriptPackOrPath"></script>
SCRIPT;
			}
		}

		return implode("\n", $buff);
	}
}
