<?php

namespace Smart\lib\fx\statics;

/**
 * A class tracking Javascript dependencies and rendering that markup
 */
class CssStatics {

	/**
	 * @var array
	 */
	public static $packages = array();

	/**
	 * @var array
	 */
	public static $cssHead	= array();

	/**
	 * Register a package as available
	 *
	 * @static
	 * @param string $name				A name for the package
	 * @param array $cssPaths		An array of paths references script files
	 */
	public static function registerPackage($name, $cssPaths) {
		if (isset(static::$packages[$name])) {
			// merge
			static::$packages[$name]	= array_merge(static::$packages[$name], $cssPaths);
		} else {
			static::$packages[$name]	= $cssPaths;
		}
	}

	/**
	 * Include a cascading-style-sheet in the </head> tag
	 * @param array|string $cssPack
	 * @return void
	 */
	public static function inclHead($cssPack) {
		if (!array_key_exists($cssPack, static::$packages)) {
			throw new \InvalidArgumentException(sprintf('CSS package "%s" does not exist.', $cssPack));
		}
		static::$cssHead[]	= $cssPack;
	}

	public static function renderHead() {
		return static::render(static::$cssHead);
	}

	/**
	 * @param array& $cssPacks		The css file to redner
	 * @return string
	 */
	private static function render(array& $cssPacks) {
		$buff	= array();

		foreach ($cssPacks as $cssPack) {
			if (stripos($cssPack, '<style') === 0) {
				$buff[]	= $cssPack;
			} else {
			foreach (static::$packages[$cssPack] as $src) {
					$buff[]	= <<<SCRIPT
	<link href="$src" rel="stylesheet">
SCRIPT;
				}
			}
		}

		return implode("\n", $buff);
	}
}
