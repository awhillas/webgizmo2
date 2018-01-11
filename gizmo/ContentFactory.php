<?php
namespace gizmo;

/**
 * Factory for building Abstract Content Tree representations.
 */
interface ContentFactory
{
	public function getAbstractContentTree($path); // : ContentObject;
}
 ?>
