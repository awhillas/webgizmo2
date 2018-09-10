<?php
namespace gizmo;

use Parsedown;


if (!defined('GIZMO_THEME')) define('GIZMO_THEME', 'default');
if (!defined('GIZMO_WEBSITE_TITLE')) define('GIZMO_WEBSITE_TITLE', 'Untitled website');


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

	public function render(ContentObject $root_node, Path $virtual_path)
	{
		$pf = new PathFinder($virtual_path);
		$node = $pf->find($root_node);
		$content = !is_null($node) ? $this->visitNode($node) : $this->visitNode($root_node);
		$what = [
			'content' => $content,
			'title' => GIZMO_WEBSITE_TITLE, // TODO: from file name + site config?
			'language' => $this->gizmo->getBestLanguage(),
		];

		return $this->template_engine->render('default', $what);
	}

	public function visitNode(ContentNode $node)
	{
		$extension = $node->getExtension();
		$children = [];

		// Bulid up an array of rendered child nodes.
		foreach($node as $file_name => $sub_node)
			if ($sub_node->getExtension())
				array_push($children, $sub_node->accept($this));  // Recurse

		$context = array(
			'content' => $node,
			'children' => $children
		);
		// Use speical extension handler if one exists + default fall backs.
		$partial_templates = [
			'partials/' . $extension,
			'partials/default-folder',
			'partials/default'
		];
		return $this->renderTemplateIfExists($partial_templates, $context);
	}

	public function visitLeaf(ContentLeaf $leaf)
	{
		// TODO: rewrite this switch as an Abstract Factory override'able by the theme
		switch($leaf->getExtension()) {
			case 'html':
				return $this->renderHtml($leaf);
			case 'md':
				return $this->renderMarkdown($leaf);
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
			case 'svg':
				return $this->renderImage($leaf);
			default:
				return 'ContentObject: ' . $leaf->getPath();
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
		return Parsedown::instance()->text($file->getContents());
	}

	private function renderImage($file)
	{
		if ($html = $this->renderTemplateIfExists(['partials/image'], [ 'file' => $file ])) return $html;
		# Fall back to vanilla img tag
		return '<img src="'.$file->directUrl().'" alt="'.$file->cleanFilename().'" class="WG__default_image" />';
	}

	private function renderHtml($file)
	{
		return $file->getContents();
	}
}
?>
