<?php
namespace gizmo;

/**
 * Abstract Factory for building Abstract Content Tree representations.
 */
interface ContentAbstractFactory
{
	public function getAbstractContentTree($path); // : FSObject;
}
 ?>
