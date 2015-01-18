<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ClassReflection;
use TYPO3\Flow\Reflection\ReflectionService;

class AggregateNormalizer {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var NormalizerInterface
	 */
	private $fallbackNormalizer;

	/**
	 * @var NormalizerInterface[]
	 */
	private $normalizers;

	public function __construct(array $normalizers, NormalizerInterface $fallbackNormalizer = NULL) {
		$this->fallbackNormalizer = $fallbackNormalizer;
		$this->normalizers        = $normalizers;
	}

	public function normalize($data) {
		if (is_array($data) || ($data instanceof \Traversable)) {
			$result = [];

			foreach ($data as $key => $value) {
				$result[$key] = $this->normalize($value);
			}
			return $result;
		} else if (is_object($data)) {
			$normalizer = $this->getNormalizerForClass(get_class($data));
			return $this->normalize($normalizer->objectToScalar($data));
		} else if (is_scalar($data) || is_null($data)) {
			return $data;
		}

		throw new \Exception('Unknown type for variable: ' . gettype($data));
	}

	/**
	 * @param string $className
	 * @return NormalizerInterface
	 * @throws \Exception
	 */
	private function getNormalizerForClass($className) {
		$class = new ClassReflection($className);
		do {
			if (array_key_exists($class->getName(), $this->normalizers)) {
				return $this->normalizers[$class->getName()];
			}
		} while ($class = $class->getParentClass());

		if (NULL !== $this->fallbackNormalizer) {
			return $this->fallbackNormalizer;
		} else {
			throw new \Exception('No normalizer for class ' . $className . ' was defined!');
		}
	}
}
