<?php
namespace gizmo;

function dump($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
};

function folder($array) {
	return implode(DIRECTORY_SEPARATOR, $array);
}

class WebGizmo
{
	private $root_dir;
	// Content Iterator
	private $content;
	// Requested content virtual path from query param.
	private $virtual_path;
	// Output format
	private $format;

	// constructor
	public function __construct(
		ContentAbstractFactory $content_source = null,
		$multi_lingual = false,
		$language = 'en',
		$theme = 'bootstrap4' // TODO: move to TemplateRenderer::__construct()
	) {
		$this->root_dir = dirname($_SERVER['SCRIPT_FILENAME']);

		$source = ($content_source ? $content_source : new LocalFileSystemContent());

		parse_str($_SERVER['QUERY_STRING'], $query);
		$this->virtual_path = $query['p'];

		$this->content = $source->getAbstractContentTree();

		$this->template = $this->getTemplateEngine($theme);
	}

	// TODO: move this to HTML Renderer as it only relates template renderers
	private function getTemplateEngine($theme_name) {
		$templates_dir = folder([$this->root_dir, 'themes', $theme_name]);
		$config = \json_decode(file_get_contents(folder([$templates_dir, 'config.json'])));
		$EngineName = "gizmo\\$config->engine";
		return new $EngineName($templates_dir);
	}

	/**
	 * Content negotiation.
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	private function getMediaType() {
		$negotiator = new \Negotiation\Negotiator();
		// TODO: make $priorities configurable
		$priorities   = array('text/html', 'application/json', 'application/xml;q=0.5');
		$mediaType = $negotiator->getBest($_SERVER['HTTP_ACCEPT'], $priorities);

		return $mediaType->getValue();
	}

	/**
	 * Factory method to get the content renderer (Visitor pattrn) class
	 */
	private function getRenderer() {
		// TODO: inject this as a method on a factory class
		switch($this->getMediaType()) {
			case 'application/json':
			case 'text/html':
			default:
				return new HtmlRender($this->virtual_path);
		}
	}

	/**
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	private function getBestLanguage() {
		$negotiator = new \Negotiation\LanguageNegotiator();
		// TODO: make $priorities configurable
		$priorities = array('en-au', 'en-gb', 'en');
		$bestLanguage = $negotiator->getBest($_SERVER['HTTP_ACCEPT_LANGUAGE'], $priorities);

		return $bestLanguage->getType();
	}

	public function render() {
		$out = '';
		$r = $this->getRenderer();
		$content = $r->render($this->content);
		$out .= $this->template->render('default', [
			'title' => 'Website title here',
			'content' => $content
		]);

		return $out;
	}
}
?>
