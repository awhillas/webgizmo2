<?php
namespace gizmo;

use gizmo\renderers\ContentRenderable;

/**
 * Ether a file (leaf) or directory (node)
 */
interface ContentObject
{
	/**
	 * The visit method of the Visitor design pattern
	 */
	public function accept(ContentRenderable $renderable);

	/**
	 * Getter for the _real_ path (Readonly, private property) which might be a
	 * local or remote path to the resource from Gizmo's point of view.
	 * Should return a Path object.
	 */
	public function getPath();

	/**
	 * URL to directly server the file using the webserver
	 * e.g. '/path/to/public_html/content/03_something.jpg' would be served as:
	 * 'www.example.com/content/03_something.jpg'
	 */
	public function getDirectUrl();

	/**
	 * How many children does the Node have?
	 */
	public function childCount();

	/**
	 * The name of the file without all the Gizmo cruff, the pretty or display name.
	 */
	public function getCleanFilename();

	/**
	 * Return the extension of the system object i.e. everything after the last '.'
	 */
	public function getExtension();
}

?>
