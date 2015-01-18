<?php
namespace Helmich\RestTools\Mvc\View;

use Helmich\RestTools\Rest\Normalizer\AggregateNormalizer;
use Helmich\RestTools\Rest\Normalizer\NormalizerInterface;
use Helmich\RestTools\Rest\Serializer\SerializerInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Response as HttpResponse;
use TYPO3\Flow\Mvc\View\AbstractView;

abstract class AbstractSerializingView extends AbstractView implements SerializingViewInterface {

	protected $variablesToRender = NULL;

	/**
	 * @var SerializerInterface
	 * @Flow\Inject
	 */
	protected $serializer;

	/**
	 * @var array
	 */
	protected $normalizers = [];

	/**
	 * @var NormalizerInterface
	 */
	protected $fallbackNormalizer = NULL;

	public function render() {
		$response = $this->controllerContext->getResponse();
		if ($response instanceof HttpResponse) {
			$response->setHeader('Content-Type', $this->serializer->getMimeType());
		}

		$normalizer = new AggregateNormalizer($this->normalizers, $this->fallbackNormalizer);

		$data = $this->getDataToRender();
		$data = $normalizer->normalize($data, $this->normalizers);

		return $this->renderNormalizedData($data);
	}

	public function registerNormalizerForClass($objectClass, NormalizerInterface $normalizer) {
		$this->normalizers[$objectClass] = $normalizer;
	}

	public function registerFallbackNormalizer(NormalizerInterface $normalizer) {
		$this->fallbackNormalizer = $normalizer;
	}

	protected function guessVariablesToRender() {
		$variablesToRender = array_keys($this->variables);

		if (FALSE !== ($key = array_search('settings', $variablesToRender))) {
			unset($variablesToRender[$key]);
		}

		$this->variablesToRender = $variablesToRender;
	}

	protected function renderNormalizedData($data) {
		return $this->serializer->serialize($data);
	}

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