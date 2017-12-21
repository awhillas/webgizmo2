<?php
namespace gizmo;

/**
 * Iterate of the different Content Sources
 */
class ContentSourceIterator implements \Iterator
{
	/**
	 * $content_path_maps = ['/' => LocalFileContent()]
	 */
	public function __construct(array $content_path_maps = array())
	{
	}

	public function rewind() {}

	public function current() {}

	public function key() {}

	public function next() {}

	public function valid() {}
}

?>
