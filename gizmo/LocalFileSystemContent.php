<?php
namespace gizmo;

/**
 * Local File System abstract factory
 */
class LocalFileSystemContent implements ContentAbstractFactory
{
	public function getAbstractContentTree($path = NULL) {
		$default = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'content';
		return new LocalFSDir(($path ? $path : $default));
	}
}

/**
 * Abstract General Local FS Object
 */
class LocalFSObject implements FSObject
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

	public function getPath() { return $this->path; }
}


/**
 * Local Directory
 */
class LocalFSDir extends LocalFSObject implements FSDir, \IteratorAggregate
{
	private $contents = [];

	function __construct($path)
	{
		try {
			parent::__construct($path);

			foreach (new \DirectoryIterator($this->path) as $file_info) {
				if($file_info->isDot()) continue;

				if ($file_info->isDir()) {
					$this->contents[$file_info->getFilename()] = new LocalFSDir($file_info->getPath());
				}
				else {
					$this->contents[$file_info->getFilename()] = new LocalFSFile($file_info);
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

	public function accept(ContentRenderer $renderer)
	{
		return $renderer->visitDir($this);
	}
}


/**
 * Local File
 */
class LocalFSFile extends LocalFSObject implements FSFile
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

	function getFilename() {
		return $this->info->getFilename();
	}

	function getExtension() {
		return $this->info->getExtension();
	}

	public function accept(ContentRenderer $renderer) {
		return $renderer->visitFile($this);
	}

	public function getContents() {
		return file_get_contents($this->info->getRealPath());
	}

}


?>
