<?php
namespace Helmich\RestTools\Mvc\View;

use Helmich\RestTools\Rest\Normalizer\RecursiveNormalizer;
use Helmich\RestTools\Rest\Normalizer\NormalizerContainer;
use Helmich\RestTools\Rest\Normalizer\NormalizerInterface;
use Helmich\RestTools\Rest\Serializer\SerializerInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Response as HttpResponse;
use TYPO3\Flow\Mvc\View\AbstractView;

/**
 * Abstract base class for views that output serialized representations of domain objects.
 *
 * @package    Helmich\RestTools
 * @subpackage Mvc\View
 */
abstract class AbstractSerializingView extends AbstractView implements SerializingViewInterface {

	protected $variablesToRender = NULL;

	/**
	 * @var SerializerInterface
	 * @Flow\Inject
	 */
	protected $serializer;

	/**
	 * @var NormalizerContainer
	 * @Flow\Inject
	 */
	protected $normalizers;

	/**
	 * @var RecursiveNormalizer
	 * @Flow\Inject
	 */
	protected $recursiveNormalizer;

	/**
	 * @var string|null
	 */
	protected $rootElement = NULL;

	/**
	 * Renders the view.
	 *
	 * This is done by applying all normalizers assigned to this view to the
	 * assigned values. This is done recursively, so you can serialize arbitrarily
	 * deeply nested object structures.
	 *
	 * @return string
	 */
	public function render() {
		$response = $this->controllerContext->getResponse();
		if ($response instanceof HttpResponse) {
			$response->setHeader('Content-Type', $this->serializer->getMimeType());
		}

		$data = $this->getDataToRender();
		$data = $this->recursiveNormalizer->normalize($data, $this->normalizers);

		return $this->serializer->serialize($data);
	}

	/**
	 * Registers a new normalizer for an entity class.
	 *
	 * Please note that the normalizer will also be applied to *subclasses* of
	 * $objectClass!
	 *
	 * @param string              $objectClass The domain entity class
	 * @param NormalizerInterface $normalizer  A normalizer instance
	 * @return void
	 */
	public function registerNormalizerForClass($objectClass, NormalizerInterface $normalizer) {
		$this->normalizers->setNormalizerForClass($objectClass, $normalizer);
	}

	/**
	 * Registers a fallback normalizer.
	 *
	 * This normalizer will be used for classes for which no explicit normalizer
	 * was registered.
	 *
	 * @param NormalizerInterface $normalizer The fallback normalizer
	 * @return void
	 */
	public function registerFallbackNormalizer(NormalizerInterface $normalizer) {
		$this->normalizers->setFallbackNormalizer($normalizer);
	}

	/**
	 * Guess the variables this view should render.
	 *
	 * By default, all variables except the settings will be rendered.
	 *
	 * @return void
	 */
	protected function guessVariablesToRender() {
		$variablesToRender = array_keys($this->variables);

		if (FALSE !== ($key = array_search('settings', $variablesToRender))) {
			unset($variablesToRender[$key]);
		}

		$this->variablesToRender = $variablesToRender;
	}

	/**
	 * Sets a root element for the serialization.
	 *
	 * @param string $element A variable name.
	 * @return void
	 */
	public function setRootElement($element) {
		$this->rootElement = $element;
	}

	/**
	 * Gets the data set to render.
	 *
	 * @return array The data set to render
	 */
	protected function getDataToRender() {
		if (NULL === $this->variablesToRender) {
			$this->guessVariablesToRender();
		}

		$data = [];
		foreach ($this->variablesToRender as $key) {
			$data[$key] =& $this->variables[$key];
		}

		if ($this->rootElement !== NULL) {
			return $data[$this->rootElement];
		}

		return $data;
	}

}
