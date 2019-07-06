<?php
namespace gizmo\renderers;

use gizmo\WebGizmo;
use gizmo\ContentNode;
use gizmo\ContentLeaf;

/**
 * Something that can render an Abstract Content Tree (ACT).
 *
 * Visitor design pattern that visits a hierarchy of ContentNode (directory) and 
 * ContentLeaf (file) objects and renders them.
 *
 * The idea being that each new format (i.e. HTML, JSON, XML) would have its own
 * Renderable visitor class.
 */
interface ContentRenderable
{
	/**
	 * Called by RenderableAbstractFactory
	 * @param WebGizmo	Singleton instance of Gizmo.
	 * @param string	An optional theme name for further specialization.
	 */
	public function __construct(WebGizmo $gizmo, string $theme_name = '');

	/**
	 * What makes this Renderable
	 *
	 * @param FSDir $root_node The root node of the Content ACT.
	 * @param string $virtual_path The virtual path to render. Optional.
	 * @return string Content ACT rendered in the specific format as a string.
	 */
	public function render(ContentNode $root_node);

	/**
	 * One of the Visitor pattern's required methods
	 *
	 * @return string The node and child nodes rendered in the format as a string
	 */
	public function visitNode(ContentNode $node);

	/**
	 * The other Visitor pattern required methods
	 *
	 * @return string The leaf node rendered in the format as a string.
	 */
	public function visitLeaf(ContentLeaf $leaf);
}
