<?php
namespace Helmich\RestTools\Mvc\View;

use Helmich\RestTools\Rest\Normalizer\NormalizerInterface;
use TYPO3\Flow\Mvc\View\ViewInterface;

/**
 * Interface definition for views that serialize domain objects.
 *
 * @package    Helmich\RestTools
 * @subpackage Mvc\View
 */
interface SerializingViewInterface extends ViewInterface {

	public function registerNormalizerForClass($objectClass, NormalizerInterface $normalizer);

	public function registerFallbackNormalizer(NormalizerInterface $normalizer);

	/**
	 * Sets a root element for the serialization.
	 *
	 * @param string $element A variable name.
	 * @return void
	 */
	public function setRootElement($element);

}