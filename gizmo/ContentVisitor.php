<?php
namespace gizmo;

interface ContentVisitor
{
	/**
	 * Visit a Node (i.e. not a leaf node)
	 * One of the Visitor pattern's required methods
	 */
	public function visitNode(ContentNode $node);

	/**
	 * Visit a leaf node (i.e. stop recursing)
	 * The other Visitor pattern's required methods
	 */
	public function visitLeaf(ContentLeaf $leaf);
}
