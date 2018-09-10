<?php
namespace gizmo;

use json_decode;

/**
 * Theme manager/wrapper
 * Mainly to handle the theme config.
 */
class Theme
{
	public $name;
	public $dir;
	public $config;
	public $root;

	function __construct($theme_name, $root_dir)
	{
		$this->name = $theme_name;
		$this->root = $root_dir;
		$this->dir = folder([$root_dir, 'themes', $theme_name]);
		$this->config = $this->getConfig();
	}

	private function getConfig()
	{
		$theme_config_file = folder([$this->dir, 'config.json']);
		if (file_exists($this->dir) and file_exists($theme_config_file))
			return json_decode(file_get_contents($theme_config_file));
		else {
			return false;
		}
	}
	/**
	 * Template inheritance.
	 * Reads the config file to check if this theme inherits from another and for that theme etc.
	 * @return Array	Paths to parent themes. 
	 */
	// private function getFolders($theme_name)
	// {
	// 	$theme = new Theme($theme_name, $this->root);
	// 	if($theme->config){
	// 		$out = [$theme->name => $theme->dir];
	// 		if (property_exists($theme->config, 'parent_theme')) {
	// 			return array_merge($out, $this->getFolders($theme->config->parent_theme));
	// 		}
	// 		return $out;
	// 	}
	// 	return [];
	// }

	public function getEngine()
	{
		$EngineName = 'gizmo\\' . $this->config->engine;
		return new $EngineName($this->dir);
	}
}