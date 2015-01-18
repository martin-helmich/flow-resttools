<?php
namespace Helmich\RestTools\Rest\Normalizer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ClassReflection;
use TYPO3\Flow\Reflection\MethodReflection;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 * Helper class that automatically normalizes objects based on public getter methods.
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Normalizer
 */
class AutoNormalizer implements NormalizerInterface {

	/**
	 * @var ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	protected $getterMethodsForClass = [];

	public function objectToScalar($object, $path = NULL) {
		$className = $this->reflectionService->getClassNameByObject($object);
		$methods   = $this->getGetterMethodsForClass($className);

		$result = [];

		foreach ($methods as $key => $methodName) {
			$result[$key] = $object->{$methodName}();
		}

		return $result;
	}

	private function getGetterMethodsForClass($className) {
		if (isset($this->getterMethodsForClass[$className])) {
			return $this->getterMethodsForClass[$className];
		}

		$class = new ClassReflection($className);

		$this->getterMethodsForClass[$className] = [];

		foreach ($class->getMethods() as $method) {
			/** @var MethodReflection $method */
			$name = $method->getName();
			$key  = NULL;

			if (substr($name, 0, 3) === 'get') {
				$key = substr($name, 3);
			} elseif (substr($name, 0, 2) === 'is') {
				$key = substr($name, 2);
			}

			if ($key !== NULL && $method->getNumberOfRequiredParameters() === 0) {
				$this->getterMethodsForClass[$className][lcfirst($key)] = $name;
			}
		}

		return $this->getterMethodsForClass[$className];
	}
}