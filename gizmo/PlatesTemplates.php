<?php
namespace gizmo;

/**
 * Plates templates
 *
 * Plates is a native PHP template system that’s fast, easy to use and easy to
 * extend.
 *
 * @see: http://platesphp.com/
 */
class PlatesTemplates implements TemplateAdaptor
{
  private $plates;

  function __construct($templates_dir, $config_options = [])
  {
    $this->plates = new \League\Plates\Engine($templates_dir);
  }

  function render($template, $variables = []) {
    if ($this->plates->exists($template)) {
      // It exists!
      return $this->plates->render($template, $variables);
    }
    else {
      return "<h1>Template not found: $template ?</h1>";
    }
  }
}
?>
