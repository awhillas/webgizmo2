<?php
namespace gizmo\renderers;

use gizmo\WebGizmo;

/**
 * Factory for Content Renderables
 * Make this a Factory so the mapping from media type can be easily redefined.
 *
 * @return ContentRenderable
 */
class RenderableFactory
{
	function get(WebGizmo $gizmo, string $media_type): ContentRenderable
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