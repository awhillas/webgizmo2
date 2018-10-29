<?php
namespace gizmo;

use Exception;
use Negotiation;

require_once('utils.php');

// TODO: rethink the language handling
if (!defined('GIZMO_LANGUAGE')) define('GIZMO_LANGUAGE', 'en');
if (!defined('GIZMO_MULTI_LINGUAL')) define('GIZMO_MULTI_LINGUAL', false);

class NotFoundException extends Exception { }

class WebGizmo
{
	// Hold an instance of the class
	private static $instance;

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
		$fs_config,
		RenderableFactory $renderable_factory = null,
		$multi_lingual = GIZMO_MULTI_LINGUAL,
		$language = GIZMO_LANGUAGE
	) {
		$this->root_dir = dirname($_SERVER['SCRIPT_FILENAME']);

		// Get the output render format
		$renderable_factory = $renderable_factory
			? $renderable_factory
			: new DefaultRenderableFactory()
		;
		$this->renderable = $renderable_factory->getRenderable($this, $this->getMediaType());

		// Which path is being requested
		parse_str($_SERVER['QUERY_STRING'], $query);
		$this->virtual_path = array_key_exists('p', $query) ? $query['p'] : '';

		// Setup filesystem(s) content
		// TODO: handle language string here somehow i.e. prepend to key in $fs_config?
		$this->content = filesystems\FileSystemFactory::get($fs_config);

		$this->finder = new PathFinder($this->content);

		// Setup singlton
		self::$instance = $this;
	}

	/**
	 * The singleton method
	 * We're not a real Singelton as we don't instanciate if instane is not set.
	 * We assume that WebGizmo is the first object to get instanciated and
	 * handles this in the __construct().
	 */
	public static function singleton()
	{
		// if (!isset(self::$instance)) {
		// 	self::$instance = new __CLASS__;
		// }
		return self::$instance;
	}

	/**
	 * Root from which Gizmo is serving from
	 */
	public function getRoot()
	{
		return $this->root_dir;
	}

	/**
	 * Content negotiation.
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	public function getMediaType() {
		$negotiator = new Negotiation\Negotiator();
		// TODO: make MediaType $priorities configurable
		$priorities   = array('text/html', 'application/json', 'application/xml;q=0.5');
		$mediaType = $negotiator->getBest($_SERVER['HTTP_ACCEPT'], $priorities);

		return $mediaType->getValue();
	}

	/**
	 * @see  http://williamdurand.fr/Negotiation/
	 */
	public function getBestLanguage() {
		$negotiator = new Negotiation\LanguageNegotiator();
		// TODO: make Language $priorities configurable
		$priorities = array('en-au', 'en-gb', 'en');
		$bestLanguage = $negotiator->getBest($_SERVER['HTTP_ACCEPT_LANGUAGE'], $priorities);

		return $bestLanguage->getType();
	}

	public function render() {
		// TODO: handle 404s i.e. convert the virtual path to a real path and check it exists.
		$node = ($this->virtual_path and $this->virtual_path !== '/')
			? $this->finder->find($this->virtual_path)
			: $this->content
		;
		if($node) {
			try {
				return $this->renderable->render($node);
			} catch (gizmo\NotFoundException $e) {
				return '<h1>404 :(</h1><p>' . $e->getMessage() .'</p>';
			} catch (Exception $e) {
				return '<pre>'.$e->getMessage().'</pre>';
			}
		}
		else
		{
			return '<h1>Content not found (404 error) :(</h1><p>Cound not find path: <strong>' . $this->virtual_path .'</strong>?</p>';
		}
	}
}
