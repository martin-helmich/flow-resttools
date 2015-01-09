<?php
namespace Helmich\RestTools\Mvc\Controller;

use Helmich\RestTools\Mvc\View\JsonView;
use Helmich\RestTools\Mvc\View\MsgpackView;
use Helmich\RestTools\Mvc\View\XmlView;
use Helmich\RestTools\Mvc\View\YamlView;
use TYPO3\Flow\Mvc\Controller\ActionController;

/**
 * Abstract base class for RESTful controllers.
 *
 * This controller offers a strong default configuration for the views and
 * content type negotiation.
 *
 * @package    Helmich\RestTools
 * @subpackage Mvc\Controller
 */
abstract class RestController extends ActionController {

	protected $viewFormatToObjectNameMap = [
		'json'    => JsonView::class,
		'yaml'    => YamlView::class,
		'msgpack' => MsgpackView::class,
	];

	protected $supportedMediaTypes = [
		'application/json',
		'application/xml',
		'application/yaml',
		'application/x-msgpack',
	];

}
