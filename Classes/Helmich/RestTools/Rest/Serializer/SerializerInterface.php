<?php
namespace Helmich\RestTools\Rest\Serializer;

/**
 * Interface definition for serializers
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Serializer
 */
interface SerializerInterface {

	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data);

	/**
	 * @param string $string
	 * @return mixed
	 */
	public function unserialize($string);

	/**
	 * Gets the IANA media typt of the serialized format.
	 *
	 * @return string
	 */
	public function getMimeType();
}