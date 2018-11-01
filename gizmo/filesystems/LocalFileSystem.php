<?php
namespace gizmo\filesystems;

use Exception;
use IteratorAggregate;
use ArrayIterator;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use gizmo\ContentLeaf;
use gizmo\ContentNode;
use gizmo\ContentObject;
use gizmo\ContentRenderable;
use gizmo\Path;
use gizmo\WebGizmo;


function dump($var) {
	echo '<pre>';
	// var_dump($var);
	var_export($var);
	echo '</pre>';
};

class LocalFilesystem implements ContentNode, IteratorAggregate
{
	public $contents;
	public $fs;
	public $node_list = array();
	public $root_node;
	public $root_path;

	public function __construct($config)
	{
		$this->root_path = new Path($config['config']['root']);
		$this->fs = new Filesystem(new Local($this->root_path));
		$this->contents = $this->fs->listContents('/', true);

		// Jerry-rig a root node
		$this->root_node = new FSDir(array(
			'path' => '',
			'extension' => '',
		), $this);

		foreach($this->contents as $fs_object)
		{
			if (in_array($fs_object['basename'], array('.DS_Store')))
				continue;
			$path = $fs_object['path'];
			$parent = $fs_object['dirname'];
			switch($fs_object['type'])
			{
				case 'dir':
					$this->node_list[$path] = new FSDir($fs_object, $this);
					break;
				case 'file':
					$this->node_list[$path] = new FSFile($fs_object, $this);
					break;
				default:
					// Throw a warning...?
					throw new Exception('Weird FS object: '.$fs_object['type'].'?');
			}
			// Build heirarchy
			if(empty($parent))
				$this->root_node->addChild($this->node_list[$path]);
			else
				$this->node_list[$parent]->addChild($this->node_list[$path]);
		}
		// dump($this->root_node);
	}

	/**
	 * Interface to create an external Iterator.
	 */
	public function getIterator()
	{
		return $this->root_node->getIterator();
	}

	// ContentObject interface methods - - - - - - - - - - - - - - - - - - //

	/**
	 * Visitor starting from the root node.
	 */
	public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitNode($this->root_node);
	}

	public function getPath()
	{
		return $this->root_path;
	}

	/**
	 * URL to the root content.
	 *
	 * TODO: Should take into consideration language code if multilingual
	 */
	public function getDirectUrl()
	{
		return '';
	}

	public function childCount()
	{
		return $this->root_node->childCount();
	}

	public function getCleanFilename()
	{
		return '';
	}

	public function getExtension()
	{
		return '';
	}

}

/**
 * Abstract General Local FS Object
 */
abstract class FSObject
{
	private $fs_object;
	public $path;

	function __construct($fs_object, LocalFilesystem $fs)
	{
		$this->fs_object = $fs_object;
		$this->fs = $fs;
		$this->path  = new Path($fs->root_path.DIRECTORY_SEPARATOR.$fs_object['path']);
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
		return preg_replace('/[^a-zA-Z0-9 -]/', '', $this->fs_object['basename']);
	}

	/**
	 * Get the file extension
	 */
	public function getExtension()
	{
		if (array_key_exists('extension', $this->fs_object))
			return $this->fs_object['extension'];
		else
			return '';
	}

	public function getFilename()
	{
		return $this->fs_object['basename'];
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
	}

	/**
	 * Add a child node.
	 */
	public function addChild(FSObject $node)
	{
		$this->contents["$node"] = $node;
	}

	/**
	 * Interface to create an external Iterator.
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->contents);
	}

	public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitNode($this);
	}

	public function childCount()
	{
		return count($this->contents);
	}
}

/**
 * Local File
 */
class FSFile extends FSObject implements ContentLeaf
{
	function __construct(array $file_info, LocalFilesystem $fs)
	{
		try {
			parent::__construct($file_info, $fs);
		} catch (Exception $e) {
			echo '<pre>Caught exception: ',  $e->getMessage(), "</pre>";
		}
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