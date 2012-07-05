<?php
/**
 * Generate a sprite map using SpriteMapper
 * http://yostudios.github.com/Spritemapper/
 */

require 'cli.php';

//PATH_STATIC . 'sprites';

function getConfigBlock($srcSpriteFolder, $destCssFolder, $destImgFolder) {
	$config	= <<<CONFIG
/* spritemapper.base_url = img/sprites/##FOLDER_NAME## */
/* spritemapper.output_image = ../webroot/img/##FILE_NAME##.png */
/* spritemapper.output_css = ../webroot/css/##FILE_NAME##.css */
CONFIG;

	$fileName	= "sprite-{$srcSpriteFolder}";
	$config		= str_ireplace('##FILE_NAME##', $fileName, $config);
	$config		= str_ireplace('##FOLDER_NAME##', "{$folderName}", $config);
}

	protected $_spriteMapperConfig = <<<CONFIG
/* spritemapper.base_url = img/sprites/##FOLDER_NAME## */
/* spritemapper.output_image = ../webroot/img/##FILE_NAME##.png */
/* spritemapper.output_css = ../webroot/css/##FILE_NAME##.css */
CONFIG;

	public $folder = null;


	public function run() {
		// get all sprite folders to scan
		$folders	= scandir($this->_spritePath);

		// collect files
		$allFiles		= array();
		foreach ($folders as $folder) {
			if (substr($folder, 0, 1) === '.' || !is_dir("{$this->_spritePath}/{$folder}")) {
				continue;
			}

			$allFiles[$folder]	= scandir("{$this->_spritePath}/{$folder}");

			// filter only png files
			$allFiles[$folder] = array_filter($allFiles[$folder], function($file) {
				if (substr($file, -4, 4) === '.png') {
					return true;
				}
				return false;
			});

			if (empty($allFiles[$folder])) {
				unset($allFiles[$folder]);
			}
		}

		// generate a css file per folder
		foreach($allFiles as $folderName => $files) {
			if (isset($this->folder) && $this->folder != $folderName) {
				continue;
			}

			$fileName	= "sprite-{$folderName}";

			// create the working template
			$filePath	= $this->_genSpriteCssTemplate($folderName, $files);

			// generate the sprite
			$this->_genSprite($filePath, $fileName);
		}
	}

	/**
	 * @param string $folderName	The name of the folder we are working on
	 * @param array $imgFiles		An array of image files
	 * @return string 		The file path
	 */
	protected function _genSpriteCssTemplate($folderName, $imgFiles) {
		$fileName	= "sprite-{$folderName}";
		$config		= $this->_spriteMapperConfig;
		$config		= str_ireplace('##FILE_NAME##', $fileName, $config);
		$config		= str_ireplace('##FOLDER_NAME##', "{$folderName}", $config);

		$output	= array($config);
		foreach ($imgFiles as $file) {
			$class					= substr($file, 0, -4);
			list($width, $height)	= getimagesize("{$this->_spritePath}/{$folderName}/{$file}");
			$output[]	= ".$class { width: {$width}px; height: {$height}px; background: url({$folderName}/{$file}); }";
		}

		$filePath	= "{$this->_spritePath}/$fileName-raw.css";
		if (is_file($filePath)) {
			unlink($filePath);
		}

		file_put_contents($filePath, implode("\n", $output));
		$this->out("Wrote sprite template: $filePath");

		return $filePath;
	}

	/**
	 * Generate the sprite assets (image and css) using "spritemapper"
	 * http://yostudios.github.com/Spritemapper/#configuration-options
	 *
	 * To install:
	 * 		sudo apt-get install python-setuptools
	 * 		sudo easy_install spritemapper
	 *
	 * @param string $pathToRawCss		Path to the css file (without extension) generated from image files in a /app/sprites sub-folder
	 * @param stirng $finalCssFileName	The name of the finalized CSS file (without extension)
	 * @return string 		The file path
	 */
	protected function _genSprite($pathToRawCss, $finalCssFileName) {
		$output	= shell_exec("spritemapper $pathToRawCss --padding=5");
		$this->out($output);
		$this->hr();

		// fix paths
		$filePath		= "app/webroot/css/{$finalCssFileName}.css";
		$scriptContents	= file_get_contents($filePath);
		$scriptContents	= str_ireplace('img/webroot/', '../', $scriptContents);

		file_put_contents($filePath, $scriptContents);
		$this->out("Wrote finalized sprite css: $filePath");

		return $filePath;
	}