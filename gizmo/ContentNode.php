<?php
namespace gizmo;

use IteratorAggregate;

/**
 * A group of ContentObjects.
 * Node with children in the Content Abstract Syntax Tree (AST) heirarchy.
 * TODO: Really want this to also `implements IteratorAggregate` but interfaces can't implement :(
 */
interface ContentNode extends ContentObject
{
    /**
	 * How many children does the Node have?
	 */
	public function childCount();
}

?>
