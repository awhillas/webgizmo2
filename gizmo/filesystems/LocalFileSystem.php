<?php
namespace gizmo\filesystems;

use \gizmo\ContentObject;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class LocalFilesystem
{
	public $fs;
	public $root;
	public $contents;

	public function __construct($config)
	{
		$this->root = new Path($config['config']['root']);
		var_dump($root);
		$this->fs = new Filesystem(new Local($root));
		$this->contents = $this->fs->listContents('/', true);
		var_dump($this->content);
		foreach($this->content as $fs_object)
		{
			switch($fs_object['type'])
			{
				case 'dir':
					$obj = new FSDir($fs_object, $this);
					break;
				case 'file':
					$obj = new FSFile($fs_object, $this);
					break;
				default:
					// Throw a warning...?
					break;
			}
		}
		die();
	}
}

/**
 * Abstract General Local FS Object
 */
abstract class FSObject implements ContentObject
{
	private $fs_object;
	public $path;

	function __construct($fs_object, LocalFilesystem $fs)
	{
		$this->fs_object = $fs_object;
		$this->path  = new Path($fs_object['path']);
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

	function __construct($fs_object, LocalFilesystem $fs)
	{
		parent::__construct($fs_object, $fs);
		$this->contents = array();
		foreach($fs->contents as $obj)
		{
			if ($obj['dirname'] == $fs_object['dirname'])
			{
				$this->contents;
			}
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
		return $this->info->getExtension();
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