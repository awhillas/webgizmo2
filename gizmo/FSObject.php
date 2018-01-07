<?php
namespace gizmo;


/**
 * Ether a file (leaf) or directory (node)
 */
interface FSObject {
	/**
	 * Getter for the path, readonly, private propeerty
	 */
	public function getPath();
}

?>
