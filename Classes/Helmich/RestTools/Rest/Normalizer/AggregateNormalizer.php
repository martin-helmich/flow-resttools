<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ClassReflection;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 * Helper class for recursively normalizing an entire data set.
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Normalizer
 */
class AggregateNormalizer {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * Normalizes a data set.
	 *
	 * This method takes a data set and a set of normalizers that should be used
	 * for different domain classes.
	 *
	 * @param mixed                 $data        The data set to normalize
	 * @param NormalizerInterface[] $normalizers A list of normalizers for classes
	 * @return array The normalized data set
	 * @throws \Exception
	 */
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
		} else if (is_scalar($data)) {
			return $data;
		}

		throw new \Exception('Unknown type for variable: ' . gettype($data));
	}

	/**
	 * Gets the normalizer to use for a given class.
	 *
	 * @todo This should be optimized! Ideas: Simple run-time cache, something like static method compilation, ...
	 *
	 * @param string $className   The class name
	 * @param array  $normalizers The normalizer configuration
	 * @return NormalizerInterface The normalizer to use
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