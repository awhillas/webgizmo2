<?php
namespace gizmo;

/**
 * File / Piece of content.
 * A Leaf Node in the Abstract Content Tree heirarchy.
 */
interface FSFile {
	/**
	 * The visited method of the Visitor design pattern
	 */
	public function accept(ContentRenderer $renderer);
	/**
	 * Getter for the readonly property file_name
	 */
	public function getFilename();

	public function getContents();
}

?>
