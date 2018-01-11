<?php
namespace gizmo;


/**
 * Ether a file (leaf) or directory (node)
 */
interface ContentObject
{
	/**
	 * The visited method of the Visitor design pattern
	 */
	public function accept(ContentRenderable $renderable);

	/**
	 * Getter for the _real_ path (Readonly, private propeerty) which might be a
	 * local or remote path to the resource from Gizmo's point of view.
	 */
	public function getPath();

	/**
	 * The virtual path of the resource. Served via Gizmo.
	 * This is the URI of the resource that Gizmo serves to the world. This is
	 * a normalised, "pretty" name.
	 * e.g. '/content/01_work.gallery' would become: '/work'
	 * and be referenced as (with Apache mod_rewrite):
	 * www.example.com/?p=/work or www.example.com/work
	 */
	public function getVirtualUrl();

	/**
	 * URL to directly server the file using the webserver
	 * e.g. '/content/03_something.jpg' would be served as:
	 * ' www.example.com/content/03_something.jpg'
	 */
	public function getDirectUrl();
}

?>