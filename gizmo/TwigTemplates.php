<?php
namespace gizmo;

/**
 * Twig templates Adaptor
 *
 * Twig is a modern template engine for PHP.
 *
 * @see: https://twig.symfony.com/
 */
class TwigTemplates implements TemplateAdaptor
{
  private twig;

  function __construct($templates_dir, $config_options = [])
  {
    $loader = new Twig_Loader_Filesystem($templates_dir);
    $this->twig = new Twig_Environment($loader, $config_options);
  }

  function render($template, $variables = []) {
    return $twig->render($template . 'html', $variables);
  }
}

?>
