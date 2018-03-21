<?php
namespace gizmo;

/**
 * Class for doing Path math.
 * Immutable.
 */
class Path
{
	private $path;
	public $is_absolute = false;

	function __construct($path, $dir_seperator = DIRECTORY_SEPARATOR)
	{
		$this->ds = $dir_seperator;
		if (is_string($path))
		{
			if (mb_substr($path, 0, 1, 'utf-8') == $dir_seperator)  // safer than $path[0]
				$this->is_absolute = true;
			$this->path = Path::convert($path);
		}
		elseif (is_array($path))
			$this->path = $path;
		else
			throw new Exception('Path expects ether a string or an Array, given: ' . gettype($path), 1);
	}

	public function __toString()
	{
		return ($this->is_absolute ? $this->ds : '') . implode($this->ds, $this->path);
	}

	public function getPath() # : Array	
	{
		return $this->path;
	}

	function convert($path) # : Array
	{
		return array_filter(explode($this->ds, $path));
	}
	
	/**
	 * Append $path_fragment to the end of the path
	 */
	public function plus(Path $other)
	{
		$new = new Path(array_merge($this->path, $other->getPath()));
		$new->is_absolute = $this->is_absolute;
		return $new;
	}
	
	public function append(string $path)
	{
		// Coz we're trying to be functional as much as possible we don't mutate.
		return new Path($this->plus($this->convert($path)));
	}
}
