<?php
namespace gizmo\renderers;

use json_decode;
use gizmo\WebGizmo;
use gizmo\ContentNode;
use gizmo\ContentLeaf;

if (!defined('GIZMO_THEME')) define('GIZMO_THEME', 'default');
if (!defined('GIZMO_WEBSITE_TITLE')) define('GIZMO_WEBSITE_TITLE', 'Untitled WebGizmo website');

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
	 * Reads the configuration file to check if this theme inherits from another and for that theme etc.
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


/**
 * HTML Content Renderable
 *
 * Visitor design pattern that recursively transverses the Abstract Content Tree
 * and renders each Node/Leaf.
 */
class HtmlRenderable implements ContentRenderable
{

	private $gizmo;
	private $template_engine;

	function __construct(WebGizmo $gizmo, $theme_name = GIZMO_THEME)
	{
		$this->gizmo = $gizmo;
		$this->template_engine = $this->getTemplateEngine($theme_name);
	}

	private function getTemplateEngine($theme_name)
	{
		$theme = new Theme($theme_name, $this->gizmo->getRoot());
		return $theme->getEngine();
	}

	public function render(ContentNode $root_node)
	{
		$content = $this->visitNode($root_node);
		$what = [
			'content' => $content,
			'title' => GIZMO_WEBSITE_TITLE, // TODO: from file name + site config?
			'language' => $this->gizmo->getBestLanguage(),
		];
		return $this->template_engine->render('default', $what);
	}

	public function visitNode(ContentNode $node)
	{
		$children = [];
		// Build up an array of rendered child nodes.
		foreach($node as $path => $sub_node)
			if ($sub_node->getExtension())
				array_push($children, $sub_node->accept($this));  // Recurs
			
		$context = array(
			'content' => $node,
			'children' => $children
		);
		// Use special extension handler if one exists + default fall backs.
		$extension = $node->getExtension();
		$partial_templates = [
			'partials/' . $extension,
			'partials/default-folder',
			'partials/default'
		];
		return $this->renderTemplateIfExists($partial_templates, $context);
	}

	public function visitLeaf(ContentLeaf $leaf)
	{
		// TODO: rewrite this switch as an Abstract Factory
		switch($leaf->getExtension()) {
			case 'htm':
			case 'html':
				return $this->renderHtml($leaf);
			case 'markdown':
			case 'mdown':
			case 'md':
				return $this->renderMarkdown($leaf);
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
			case 'svg':
				return $this->renderImage($leaf);
			default:
				// TODO: Use an <object> tag and detect the mime based on the file extension.
				return 'Content Leaf:' . $leaf->getPath() . "<strong>" . $leaf->getFilename() . "</strong>";
		}
	}

	private function renderTemplateIfExists($partial_templates, $context = [])
	{
		foreach ($partial_templates as $template)
			if ($this->template_engine->canHandle($template))
				return $this->template_engine->render($template, $context);

		return false;
	}

	private function renderMarkdown($file)
	{
		return \Parsedown::instance()->text($file->getContents());
	}

	private function renderImage($file)
	{
		$html = $this->renderTemplateIfExists(['partials/image'], [ 'file' => $file ]);
		if ($html)
			return $html;
		# Fall back to vanilla img tag
		return '<img src="'.$file->getDirectUrl().'" alt="'.$file->getCleanFilename().'" class="WG__default_image" />';
	}

	private function renderHtml($file)
	{
		return $file->getContents();
	}
}
?>
