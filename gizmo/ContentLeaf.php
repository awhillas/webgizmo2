<?php
namespace gizmo;

/**
 * File / Piece of content.
 * A Leaf Node in the Abstract Content Tree heirarchy.
 */
interface ContentLeaf extends ContentObject
{
	/**
	 * Getter for the readonly property file_name
	 */
	public function getFilename();
}

/**
 *
 */
interface ContentTextLeaf extends ContentLeaf
{
	/**
	 * Text files have content that we want to transform (e.g. Markdown to HTML)
	 * and thus we want to read their contents.
	 */
	public function getContents();
}

?>
