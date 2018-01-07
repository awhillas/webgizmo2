<?php
namespace gizmo;

/**
 * Visitor design pattern that renders an Abstract Content Tree
 */
interface ContentRenderer
{
	public function render(FSDir $node);
	public function visitDir(FSDir $node);
	public function visitFile(FSFile $leaf);
}


?>
