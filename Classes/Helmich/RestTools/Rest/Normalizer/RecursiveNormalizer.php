<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;

class RecursiveNormalizer {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	protected $stack = [];

	public function normalize($data, NormalizerContainer $normalizers) {
		if (is_array($data) || ($data instanceof \Traversable)) {
			$result = [];

			foreach ($data as $key => $value) {
				$this->pushPath($key);
				$result[$key] = $this->normalize($value, $normalizers);
				$this->popPath();
			}
			return $result;
		} else if (is_object($data)) {
			$normalizer = $normalizers->getNormalizerForClass(get_class($data));
			return $this->normalize($normalizer->objectToScalar($data, $this->path()), $normalizers);
		} else if (is_scalar($data) || is_null($data)) {
			return $data;
		}

		throw new \Exception('Unknown type for variable: ' . gettype($data));
	}

	private function pushPath($path) {
		if (is_numeric($path)) {
			$path = '*';
		}
		array_push($this->stack, $path);
	}

	private function popPath() {
		array_pop($this->stack);
	}

	private function path() {
		return implode('.', $this->stack);
	}

}
