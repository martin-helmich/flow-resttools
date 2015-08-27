<?php
namespace Helmich\RestTools\Annotations;

/**
 * Class BodyParam
 *
 * @package Helmich\RestTools
 * @subpackage Annotations
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