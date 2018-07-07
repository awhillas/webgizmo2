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
}

?>
