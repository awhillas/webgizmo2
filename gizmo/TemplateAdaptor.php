<?php
namespace gizmo;

/**
 * Interface used for template rendering.
 * Adaptor design pattern.
 */
interface TemplateAdaptor
{
	/**
   * @param string $template Template name.
   * @param  array $variables hash in the form of array('variable' => 'value').
	 * @return string Rendered template with with the given vaiables.
	 */
  function render($template, $variables = []);
  /**
   * Can handle the template/extension
   */
  function canHandle($template_name);
}
?>
