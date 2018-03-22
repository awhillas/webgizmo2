<?php
namespace gizmo;

/**
 * Renders the Content AST as JSON
 */
class JsonRenderable extends ContentRenderable
{
	public function render(FSDir $root_node)
	{
		return $this->visitNode($root_node);
	}

	public function visitNode(FSDir $node)
	{
		$children = [];
		foreach($node as $file_name => $sub_node) {
			$children .= $sub_node->accept($this);  // Recurse
		}

		return '{
			"name": "' . $this->getFilename() . '",
			"children": [
				' . implode(',', $children) . '
			]
		}';
	}

	public function visitLeaf(FSFile $leaf)
	{
		return '{ "name": "' . $this->getFilename() . '" }';
	}
}

?>
