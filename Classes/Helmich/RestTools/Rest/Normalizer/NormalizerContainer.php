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

	/**
	 * @var NormalizerInterface[]
	 */
	protected $normalizers;

	/**
	 * @var NormalizerInterface
	 */
	protected $fallbackNormalizer;

	/**
	 * Registers a normalizer for a given class.
	 *
	 * @param string              $className  The class name
	 * @param NormalizerInterface $normalizer The normalizer
	 * @return void
	 */
	public function setNormalizerForClass($className, NormalizerInterface $normalizer) {
		$this->normalizers[$className] = $normalizer;
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
	public function setFallbackNormalizer(NormalizerInterface $normalizer) {
		$this->fallbackNormalizer = $normalizer;
	}

	/**
	 * Gets the normalizer to use for a given class.
	 *
	 * @param string $className The class name
	 * @return NormalizerInterface The normalizer to use
	 * @throws \Exception
	 */
	public function getNormalizerForClass($className) {
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

		if (NULL !== $this->fallbackNormalizer) {
			return $this->fallbackNormalizer;
		} else {
			throw new \Exception('No normalizer for class ' . $className . ' was defined!');
		}
	}

} 