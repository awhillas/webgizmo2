<?php
namespace gizmo;

use Iterator;
use Exception;

/**
 * Class for doing Path math.
 * Immutable.
 */
class Path implements Iterator
{
	private $path;
	private $position = 0; // For the iterator interface
	public $is_absolute = false;
	private $ds = DIRECTORY_SEPARATOR;

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
			$this->path = array_values($path); // so always start index at 0
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

	public function length()
	{
		return count($this->path);
	}

	function convert($path) # : Array
	{
		return array_values(array_filter(explode($this->ds, $path)));
	}

	function head()
	{
		return (count($this->path)) ? reset($this->path) : '';
	}

	function tail()
	{
		return (count($this->path)) ?  array_slice($this->path, -1)[0] : '';
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

	/**
	 * Remove the given head (prefix) path from this path.
	 * If this path is not prefixed by the $head then trigger a warning
	 */
	public function decapitate(Path $head)
	{
		if ($this->hasPrefix($head))
			return new Path(array_slice($this->path, $head->length()));
		else
			trigger_error((string)$head." is not a prefix of ".(string)$this." :(", E_USER_WARNING);
	}

	/**
	 * Check if the given Path is a prefix	to this Path.
	 */
	function hasPrefix(Path $prefix)
	{
		if ($this->length() < $prefix->length())
			return false;

		$prefix->rewind();
		$this->rewind();

		while ($prefix->valid())
		{
			if($prefix->current() !== $this->current())
				return false;

			$prefix->next();
			$this->next();
		}
		return true;
	}

	public function append(string $path)
	{
		// Coz we're trying to be functional as much as possible we don't mutate.
		return new Path($this->plus($this->convert($path)));
	}

	/**
	 * Path less the head
	 */
	public function shift()
	{
		return new Path(array_slice($this->path, 1));
	}

	public function equals(Path $other)
	{
		return $this->path === $other->path;
	}

	// Iterator interface - - - - - - - - - - //

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->path[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->path[$this->position]);
    }
}
