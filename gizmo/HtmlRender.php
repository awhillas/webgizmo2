<?php
namespace gizmo;

/**
 * HTML Content Renderer
 *
 * Visitor design pattern that recuriscly dedends over the Abstract Content Tree
 * and renders each Node/Leaf.
 */
class HtmlRender implements ContentRenderer
{
	private $vpath;

	function __construct($virtual_path = '/') {
		if (!is_string($virtual_path))
			throw new Exception('Virtual path must be a string', 1);
		$this->vpath = $virtual_path;
	}

	public function render(FSDir $root_node) {
		return $this->visitDir($root_node);
	}

	public function visitDir(FSDir $node) {
		$out = '';
		// $out = '<p>FSDir:' . $node->getPath() . '</p>';

		// Children
		foreach($node as $file_name => $sub_node) {
			$out .= $sub_node->accept($this);  // Recurse
		}

		return $out;
	}

	public function visitFile(FSFile $leaf) {
		// TODO: rewrite this
		switch($leaf->getExtension()) {
			case 'md':
				return $this->renderMarkdown($leaf);
			default:
				return 'FSFile:' . $leaf->getPath() . "<strong>/" . $leaf->getFilename() . "</strong>";
		}
	}

	private function renderMarkdown($file) {
		return \Parsedown::instance()->text($file->getContents());
	}
}

?>
