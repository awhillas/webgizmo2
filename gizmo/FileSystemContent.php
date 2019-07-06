<?php
namespace gizmo;

use pathinfo;
use Exception;
use IteratorAggregate;
use ArrayIterator;

use gizmo\renderers\ContentRenderable;


if (!defined('GIZMO_CONTENT_DIR')) define('GIZMO_CONTENT_DIR', 'content');


/**
 * File System abstract factory
 */
class FileSystemContent implements ContentFactory
{
	/**
	 * @param path_append	to be appended to the base content path i.e. langauge code 'en', 'de' etc.
	 */
	public function getAbstractContentTree($path_append = NULL) // : ContentObject
	{
		$base_path = new Path(folder([dirname($_SERVER['SCRIPT_FILENAME']), GIZMO_CONTENT_DIR]));
		return new FSDir($path_append ? $base_path->plus(new Path($path_append)) : $base_path);
	}
}

/**
 * Abstract General Local FS Object
 */
abstract class FSObject implements ContentLeaf
{
	public $path;

	function __construct($path)
	{
		if(file_exists($path))
		{
			$this->path = $path;
		}
		else
		{
			throw new Exception('Path does not exist?' . $path, 1);
		}
	}

	public function __toString()
	{
		return (string)$this->path;
	}

	abstract public function accept(ContentRenderable $renderable);

	public function getPath()
	{
		return $this->path;
	}

	public function getDirectUrl()
	{
		return ''; // TODO: add '/content' directory and use ugly name.
	}

	public function getCleanFilename()
	{
		// TODO: replace this with a parser
		return preg_replace('/[^a-zA-Z0-9 -]/', '', pathinfo($this->getPath(),	PATHINFO_FILENAME));
	}
}


/**
 * Local Directory
 */
class FSDir extends FSObject implements ContentNode, IteratorAggregate
{
	private $contents = [];

	function __construct($path)
	{
		try {
			parent::__construct($path);

			foreach (new \DirectoryIterator((string)$this->path) as $file_info) {
				if($file_info->isDot()) continue;
				if($file_info->getFilename()[0] == '.') continue;

				if ($file_info->isDir()) {
					$this->contents[$file_info->getFilename()] = new FSDir(new Path($file_info->getRealPath()));
				}
				else {
					$this->contents[$file_info->getFilename()] = new FSFile($file_info);
				}
			}
			ksort($this->contents);
			// echo '<pre>'; var_dump($this->contents); echo '</pre>';
		}
		catch (Exception $e) {
			echo '<pre>Caught exception: ',  $e->getMessage(), "</pre>";
		}
	}

	public function getIterator()
	{
		return new ArrayIterator($this->contents);
	}

	public function childCount()
	{
		return count($this->contents);
	}

	function getExtension() {
		return pathinfo($this->getPath(), PATHINFO_EXTENSION);
	}

	public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitNode($this);
	}
}


/**
 * Local File
 */
class FSFile extends FSObject implements ContentLeaf
{
	public $info; // SplFileInfo

	function __construct(\SplFileInfo $file_info)
	{
		try {
			parent::__construct($file_info->getPath());
			$this->info = new \SplFileInfo($file_info->getRealPath());
		} catch (Exception $e) {
			echo '<pre>Caught exception: ',  $e->getMessage(), "</pre>";
		}
	}

	public function getPath()
	{
		return new Path($this->info->getRealPath());
	}

	function getFilename()
	{
		return $this->info->getFilename();
	}

	function getExtension()
	{
		return $this->info['extension'];
	}

	public function getDirectUrl()
	{
		return str_replace(WebGizmo::singleton()->getRoot(), '', $this->getPath());
	}

	public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitLeaf($this);
	}

	public function getContents()
	{
		return file_get_contents($this->getPath());
	}

	public function childCount()
	{
		return 0;
	}

}