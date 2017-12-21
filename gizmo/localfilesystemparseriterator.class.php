<?php


/**
 * Parses a local folder into a Content AST Tree of FSFile and FSDir objects
 */
class LocalFileSystemIterator implements \Iterator
{
	private $path;

	function __construct(string $pathToContentRoot = '')
	{
		// Default Content source.
		$this->path = ($pathToContentRoot ? $pathToContentRoot: $_SERVER['DOCUMENT_ROOT'] . '/content');
		if (!file_exists($this->path))
			throw new Exception("Content path does not exist? $this->path", 1);
	}
}


 ?>
