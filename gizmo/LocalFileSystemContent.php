<?php
namespace gizmo;

/**
 * Local File System abstract factory
 */
class LocalFileSystemContent implements ContentFactory
{
	public function getAbstractContentTree($path = NULL) {
		$default = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'content';
		return new FSDir(($path ? $path : $default));
	}
}

/**
 * Abstract General Local FS Object
 */
abstract class FSObject implements ContentObject
{
	protected $path;

	function __construct($path)
	{
		if(file_exists($path)) {
			$this->path = $path;
		}
		else {
			throw new Exception('Path does not exist?' . $path, 1);
		}
	}

	abstract public function accept(ContentRenderable $renderable);

	public function getPath()
	{
		return $this->path;
	}

	public function getVirtualUrl()
	{
		return ''; // TODO: convert real (ugly) file name to virtual filename.
	}

	public function getDirectUrl()
	{
		return ''; // TODO: add '/content' directory and use ugly name.
	}
}


/**
 * Local Directory
 */
class FSDir extends FSObject implements ContentNode, \IteratorAggregate
{
	private $contents = [];

	function __construct($path)
	{
		try {
			parent::__construct($path);

			foreach (new \DirectoryIterator($this->path) as $file_info) {
				if($file_info->isDot()) continue;

				if ($file_info->isDir()) {
					$this->contents[$file_info->getFilename()] = new FSDir($file_info->getPath());
				}
				else {
					$this->contents[$file_info->getFilename()] = new FSFile($file_info);
				}
			}
			// ksort($this->contents);
			// echo '<pre>'; var_dump($this->contents); echo '</pre>';
		}
		catch (Exception $e) {
			echo '<pre>Caught exception: ',  $e->getMessage(), "</pre>";
		}
	}

	public function getIterator()
	{
			return new \ArrayIterator($this->contents);
	}

	public function accept(ContentRenderable $renderable)
	{
		return $renderable->visitDir($this);
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
		return $this->info->getRealPath();
	}

	function getFilename() {
		return $this->info->getFilename();
	}

	function getExtension() {
		return $this->info->getExtension();
	}

	public function accept(ContentRenderable $renderable) {
		return $renderable->visitFile($this);
	}

	public function getContents()
	{
		return file_get_contents($this->getPath());
	}
}


?>
