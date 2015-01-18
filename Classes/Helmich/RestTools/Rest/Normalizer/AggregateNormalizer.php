<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\Flow\Reflection\ClassReflection;
use TYPO3\Flow\Reflection\ReflectionService;

class AggregateNormalizer {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	public function normalize($data, array $normalizers) {
		if (is_array($data) || ($data instanceof \Traversable)) {
			$result = [];

			foreach ($data as $key => $value) {
				$result[$key] = $this->normalize($value, $normalizers);
			}
			return $result;
		} else if (is_object($data)) {
			$normalizer = $this->getNormalizerForClass(get_class($data), $normalizers);
			return $this->normalize($normalizer->objectToScalar($data), $normalizers);
		} else if (is_scalar($data) || is_null($data)) {
			return $data;
		}

		throw new \Exception('Unknown type for variable: ' . gettype($data));
	}

	/**
	 * @param       $className
	 * @param array $normalizers
	 * @return NormalizerInterface
	 * @throws \Exception
	 */
	private function getNormalizerForClass($className, array $normalizers) {
		$class = new ClassReflection($className);
		do {
			if (array_key_exists($class->getName(), $normalizers)) {
				return $normalizers[$class->getName()];
			}
		} while ($class = $class->getParentClass());

		throw new \Exception('No normalizer for class ' . $className . ' was defined!');
	}
}