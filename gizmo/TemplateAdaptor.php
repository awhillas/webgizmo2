<?php
namespace gizmo;

/**
 * Interface used for template rendering.
 * Adaptor design pattern.
 * TODO: template inheritance!
 */
interface TemplateAdaptor
{
	function __construct($templates_dir, $config_options = []);
  function render($template, $variables = []);
}
?>
