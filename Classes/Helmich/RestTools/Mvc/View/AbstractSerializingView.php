<?php
namespace Helmich\RestTools\Mvc\View;

use Helmich\RestTools\Rest\Normalizer\AggregateNormalizer;
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
	 * @var AggregateNormalizer
	 * @Flow\Inject
	 */
	protected $normalizer;

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
		$data = $this->normalizer->normalize($data, $this->normalizers);

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
		$this->normalizers->put($objectClass, $normalizer);
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

		return $data;
	}

}
