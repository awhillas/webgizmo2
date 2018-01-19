<?php
namespace gizmo;

/**
 * Something that can render an Abstract Content Tree.
 *
 * Visitor design pattern that visits a heirarchy of FSDir (node) and FSFile
 * (leaf) objects and renders them.
 *
 * The idea being that each new format (i.e. HTML, JSON, XML) would have its own
 * Renderable visitor class.
 */
interface ContentRenderable
{
	/**
	 * What makes this Renderable
	 *
	 * @param FSDir $root_node The root node of the Content AST.
	 * @param	string $virtual_path The vitual path to render. Optional.
	 * @return string Content AST rendered in the specific format as a string.
	 */
	public function render(FSDir $root_node);

	/**
	 * One of the Visitor pattern's required methods
	 *
	 * @return string The node and child nodes rendered in the format as a string
	 */
	public function visitDir(FSDir $node);

	/**
	 * The other Visitor pattern required methods
	 *
	 * @return string The leaf node rendered in the format as a string.
	 */
	public function visitFile(FSFile $leaf);
}


?>
