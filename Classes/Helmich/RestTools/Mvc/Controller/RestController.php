<?php
namespace Helmich\RestTools\Mvc\Controller;

use Helmich\RestTools\Annotations\BodyParam;
use Helmich\RestTools\Mvc\View\JsonView;
use Helmich\RestTools\Mvc\View\MsgpackView;
use Helmich\RestTools\Mvc\View\YamlView;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Mvc\Exception\RequiredArgumentMissingException;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Annotations as Flow;

/**
 * Abstract base class for RESTful controllers.
 *
 * This controller offers a strong default configuration for the views and
 * content type negotiation.
 *
 * @package    Helmich\RestTools
 * @subpackage Mvc\Controller
 */
abstract class RestController extends ActionController {

	protected $viewFormatToObjectNameMap = [
		'json'    => JsonView::class,
		'yaml'    => YamlView::class,
		'msgpack' => MsgpackView::class,
	];

	protected $supportedMediaTypes = [
		'application/json',
		'application/xml',
		'application/yaml',
		'application/x-msgpack',
	];

	/**
	 * Maps arguments delivered by the request object to the local controller arguments.
	 *
	 * @return void
	 * @throws RequiredArgumentMissingException
	 * @api
	 */
	protected function mapRequestArgumentsToControllerArguments() {
		$bodyParameters = static::getBodyParameterMappings($this->objectManager);

		/** @var BodyParam $bodyArgument */
		$bodyArgument = NULL;
		if (isset($bodyParameters[$this->actionMethodName])) {
			$bodyArgument = $bodyParameters[$this->actionMethodName];
		}

		foreach ($this->arguments as $argument) {
			$argumentName = $argument->getName();
			if ($this->request->hasArgument($argumentName)) {
				$argument->setValue($this->request->getArgument($argumentName));
			} elseif ($bodyArgument !== NULL && $argumentName == $bodyArgument->argumentName) {
				if ($bodyArgument->allowAllProperties) {
					$config = $argument->getPropertyMappingConfiguration();
					$config->allowAllProperties();
				}

				$value = $this->unserializeBody($argument->getDataType());
				$argument->setValue($value);

			} elseif ($argument->isRequired()) {
				throw new RequiredArgumentMissingException('Required argument "' . $argumentName  . '" is not set.', 1298012500);
			}
		}
	}

	private function unserializeBody($targetType) {
		$http = $this->request->getHttpRequest();
		$bodyString = $http->getContent();
		switch ($http->getHeader('Content-Type')) {
			case 'application/json':
				return json_decode($bodyString, TRUE);
			default:
				return $bodyString;
		}
	}

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return array
	 * @Flow\CompileStatic
	 */
	static public function getBodyParameterMappings($objectManager) {
		/** @var ReflectionService $reflectionService */
		$reflectionService = $objectManager->get(ReflectionService::class);

		$className = get_called_class();
		$methodNames = get_class_methods($className);

		$results = [];

		foreach ($methodNames as $methodName) {
			/** @var BodyParam $annotation */
			$annotation = $reflectionService->getMethodAnnotation($className, $methodName, BodyParam::class);
			if ($annotation !== NULL) {
				$results[$methodName] = $annotation;
			}
		}

		return $results;
	}

}
