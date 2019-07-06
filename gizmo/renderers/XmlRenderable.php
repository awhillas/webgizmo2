<?php
namespace gizmo;

/**
 * Render Content Tree as XML
 */
class XmlRenderable extends ContentRenderable
{
	function __construct(WebGizmo $gizmo, $theme_name = GIZMO_THEME)
	{
		$this->gizmo = $gizmo;
	}

	public function render(FSDir $root_node, $virtual_path = '')
	{
		return $this->visitNode($root_node);
	}

	public function visitNode(FSDir $node)
	{
		$children = array();

		foreach($node as $file_name => $sub_node)
			if ($sub_node->getExtension())  // render if parent is
				array_push($children, $sub_node->accept($this));  // Recurse

		return '<dir name="' . $node->getFilename() . '">' . implode("\n", $children) . '</dir>';
	}

	public function visitLeaf(FSFile $leaf)
	{
		return '<file>' . $leaf->getFilename . '</file>';
	}
}

?>
