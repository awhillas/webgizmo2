<?php
namespace gizmo;

use Exception;

/**
 * Plates templates
 *
 * Plates is a native PHP template system that’s fast, easy to use and easy to
 * extend.
 *
 * @see: http://platesphp.com/
 */
class Plates3Templates implements TemplateAdaptor
{
  private $plates;

  /**
   * @param array Hash of [template => path, ...] for theme inheritance.
   */
  function __construct($parent_theme_dir, $config_options = [])
  {
    $this->plates = new \League\Plates\Engine($parent_theme_dir);
    foreach($config_options as $name => $path)
      $this->plates->addFolder($name, $path, true);
  }

  function render($template, $variables = [])
  {
    if (!is_array($variables))
      throw new Exception('$variables should be an array.' . dump($variables), 1);

    if ($this->plates->exists($template)) {
      // It exists!
      return $this->plates->render($template, $variables);
    }
    else {
      return "<h1>Template not found: '$template'?</h1>";
    }
  }

  /**
   * Numeric extensions are # of columns. eg. "something.columns.3"
   * Else one-to-one mapping between extension and template names.
   * @return boolean Weather the extension is handled by a template or not
   */
  function canHandle($extension)
  {
    return is_numeric($extension) or $this->plates->exists($extension);
  }
}
?>
