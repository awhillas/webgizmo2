<?php
namespace gizmo;

/**
 * Directory a group of FSObjects.
 * Node in the Abstract Content Tree heirarchy.
 */
interface FSDir {
	/**
	 * The visited method of the Visitor design pattern
	 */
	public function accept(ContentRenderer $renderer);
}

?>
