<?php
namespace gizmo;

/**
 * HTML Content Renderable
 *
 * Visitor design pattern that recuriscly dedends over the Abstract Content Tree
 * and renders each Node/Leaf.
 */
class HtmlRenderable implements ContentRenderable
{

	private $gizmo;
	private $template_engine;

	function __construct(WebGizmo $gizmo, $theme_name = 'default')
	{
		$this->gizmo = $gizmo;
		$this->template_engine = $this->getTemplateEngine($theme_name);
	}

	private function getTemplateEngine($theme_name)
	{
		$templates_dir = folder([$this->gizmo->getRoot(), 'themes', $theme_name]);
		$config = \json_decode(file_get_contents(folder([$templates_dir, 'config.json'])));
		$EngineName = "gizmo\\$config->engine";
		return new $EngineName($templates_dir, $config);
	}

	public function render(FSDir $root_node, $virtual_path = '')
	{
		if (!is_string($virtual_path))
			throw new \Exception('Virtual path must be a string', 1);
		$content = $this->visitDir($root_node);
		$what = [
			'content' => $content,
			'title' => 'WebGizmo default theme', // from file name + site config?
			'language' => $this->gizmo->getBestLanguage(),
		];
		return $this->template_engine->render('default', $what);
	}

	public function visitDir(FSDir $node)
	{
		$out = '';
		// $out = '<p>FSDir:' . $node->getPath() . '</p>';
		// Children
		foreach($node as $file_name => $sub_node)
		{
			$out .= $sub_node->accept($this);  // Recurse
		}

		return $out;
	}

	public function visitFile(FSFile $leaf)
	{
		// TODO: rewrite this switch
		switch($leaf->getExtension()) {
			case 'md':
				return $this->renderMarkdown($leaf);
			default:
				return 'FSFile:' . $leaf->getPath() . "<strong>/" . $leaf->getFilename() . "</strong>";
		}
	}

	private function renderMarkdown($file)
	{
		return \Parsedown::instance()->text($file->getContents());
	}
}
?>
