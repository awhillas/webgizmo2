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
	/**
	 * Renderable of the content AST
	 */
	private $renderable;

	// constructor
	public function __construct(
		ContentFactory $content_source = null,
		RenderableFactory $renderable_factory = null,
		$multi_lingual = false,
		$language = 'en'
	) {
		$this->root_dir = dirname($_SERVER['SCRIPT_FILENAME']);

		$source = ($content_source ? $content_source : new LocalFileSystemContent());

		$renderable_factory = ($renderable_factory
			? $renderable_factory
			: new DefaultRenderableFactory()
		);
		$this->renderable = $renderable_factory->getRenderable($this, $this->getMediaType());

		parse_str($_SERVER['QUERY_STRING'], $query);
		$this->virtual_path = $query['p'];

		$this->content = $source->getAbstractContentTree();
	}

	public function getRoot()
	{
		return $this->root_dir;
	}

	/**
	 * Content negotiation.
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	public function getMediaType() {
		$negotiator = new \Negotiation\Negotiator();
		// TODO: make $priorities configurable
		$priorities   = array('text/html', 'application/json', 'application/xml;q=0.5');
		$mediaType = $negotiator->getBest($_SERVER['HTTP_ACCEPT'], $priorities);

		return $mediaType->getValue();
	}

	/**
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	public function getBestLanguage() {
		$negotiator = new \Negotiation\LanguageNegotiator();
		// TODO: make $priorities configurable
		$priorities = array('en-au', 'en-gb', 'en');
		$bestLanguage = $negotiator->getBest($_SERVER['HTTP_ACCEPT_LANGUAGE'], $priorities);

		return $bestLanguage->getType();
	}

	public function render() {
		return $this->renderable->render($this->content, $this->virtual_path);
	}
}
?>
