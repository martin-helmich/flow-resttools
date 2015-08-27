<?php
namespace Helmich\RestTools\Annotations;

/**
 * Annotation denoting controller arguments to build from the request body
 *
 * This annotation is intended to be used to denote controller action arguments
 * that should be mapped directly from the request body.
 *
 * @package Helmich\RestTools
 * @subpackage Annotations
 * @author Martin Helmich <typo3@martin-helmich.de>
 *
 * @Annotation
 * @Target("METHOD")
 */
final class BodyParam {

	/**
	 * @var string
	 */
	public $argumentName;

	/**
	 * @var bool
	 */
	public $allowAllProperties = FALSE;

	public function __construct(array $values) {
		if (isset($values['value']) || isset($values['argumentName'])) {
			$this->argumentName = ltrim(isset($values['argumentName']) ? $values['argumentName'] : $values['value'], '$');
		}

		if (isset($values['allowAllProperties'])) {
			$this->allowAllProperties = (bool) $values['allowAllProperties'];
		}
	}

}