<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Reflection\ClassReflection;

/**
 * Simple container class for object normalizers.
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Normalizer
 */
class NormalizerContainer {

	protected $normalizers;

	/**
	 * Registers a normalizer for a given class.
	 *
	 * @param string              $className  The class name
	 * @param NormalizerInterface $normalizer The normalizer
	 * @return void
	 */
	public function put($className, NormalizerInterface $normalizer) {
		$this->normalizers[$className] = $normalizer;
	}

	/**
	 * Gets the normalizer to use for a given class.
	 *
	 * @param string $className The class name
	 * @return NormalizerInterface The normalizer to use
	 * @throws \Exception
	 */
	public function get($className) {
		if (array_key_exists($className, $this->normalizers)) {
			return $this->normalizers[$className];
		}

		$class = new ClassReflection($className);
		do {
			if (array_key_exists($class->getName(), $this->normalizers)) {
				$this->normalizers[$className] = $this->normalizers[$class->getName()];
				return $this->normalizers[$className];
			}
		} while ($class = $class->getParentClass());

		throw new \Exception('No normalizer for class ' . $className . ' was defined!');
	}

} 