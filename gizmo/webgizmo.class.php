<?php
namespace gizmo;

class WebGizmo
{
	private $virtual_path;
	private $root_dir;

	// constructor
	public function __construct($content_map = array()) {
		parse_str($_SERVER['QUERY_STRING'], $query);
		$this->virtual_path = $query['p'];
		$this->root_dir = $_SERVER['DOCUMENT_ROOT'];
		$this->content = new ContentIterator($content_map);
	}

	public function render() {
		$out = '';
		foreach (new \DirectoryIterator('./gizmo') as $fileInfo) {
			if($fileInfo->isDot()) continue;
			$out .= '<p>' . $fileInfo->getFilename() . '</p>';
		}
		return "I'm WebGizmo 2! $out";
	}
}
?>
