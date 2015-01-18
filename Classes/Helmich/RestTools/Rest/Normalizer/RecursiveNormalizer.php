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

	public function normalize($data, NormalizerContainer $normalizers) {
		if (is_array($data) || ($data instanceof \Traversable)) {
			$result = [];

			foreach ($data as $key => $value) {
				$result[$key] = $this->normalize($value, $normalizers);
			}
			return $result;
		} else if (is_object($data)) {
			$normalizer = $normalizers->getNormalizerForClass(get_class($data));
			return $this->normalize($normalizer->objectToScalar($data), $normalizers);
		} else if (is_scalar($data) || is_null($data)) {
			return $data;
		}

		throw new \Exception('Unknown type for variable: ' . gettype($data));
	}

}
