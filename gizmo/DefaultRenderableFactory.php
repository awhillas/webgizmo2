<?php
namespace gizmo;

/**
 * Interface for Renderable (Renderer?) Facorys
 * TODO: move this to its own file if i ever make more than one RenderableFactory
 */
interface RenderableFactory
{
	/**
	 * Factory method to get a content renderable object
	 * By abstracting it to a Factory class we can make it configurable in the
	 * future by passing it into the
	 */
	public function getRenderable(WebGizmo $gizmo, $media_type);
}


/**
 * Factory for Content Renderabls
 * Make this a Factory so the mapping from media type can be easily redefined.
 */
class DefaultRenderableFactory implements RenderableFactory
{
	function getRenderable(WebGizmo $gizmo, $media_type)
	{
		switch($media_type)
		{
			case 'application/json':
				return new JsonRenderable($gizmo);
			case 'text/html':
			default:
				return new HtmlRenderable($gizmo);
		}
	}
}

 ?>
