<?php
namespace gizmo\renderers;


/**
 * Renders the Content AST as JSON
 */
class JsonRenderable extends ContentRenderable
{
	public function render(ContentNode $root_node)
	{
		return $this->visitNode($root_node);
	}

	public function visitNode(ContentNode $node)
	{
		$children = [];
		foreach($node as $file_name => $sub_node) {
			$children .= $sub_node->accept($this);  // Recurs
		}

		return '{
			"name": "' . $this->getFilename() . '",
			"children": [
				' . implode(',', $children) . '
			]
		}';
	}

	public function visitLeaf(ContentLeaf $leaf)
	{
		return '{ "name": "' . $this->getFilename() . '" }';
	}
}

?>
