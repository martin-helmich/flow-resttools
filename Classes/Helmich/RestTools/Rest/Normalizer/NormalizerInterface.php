<?php
namespace Helmich\RestTools\Rest\Normalizer;

/**
 * Interface definition for domain object serializers.
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Normalizer
 */
interface NormalizerInterface {

	/**
	 * Converts a domain object to a scalar value.
	 *
	 * @param string $object The domain object
	 * @return mixed The mapped scalar value
	 */
	public function objectToScalar($object);

}