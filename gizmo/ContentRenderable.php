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
interface ContentRenderable extends ContentVisitor
{
	/**
	 * What makes this Renderable
	 *
	 * @param	FSDir	$root_node The root node of the Content AST.
	 * @param	string	$virtual_path The vitual path to render. Optional.
	 * @return	string	Content AST rendered in the specific format as a string.
	 */
	public function render(ContentObject $root_node, Path $virtual_path);
}
