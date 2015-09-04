<?php
namespace Helmich\RestTools\Rest\Serializer;

/**
 * JSON serializer.
 *
 * @package    Helmich\RestTools
 * @subpackage Rest\Serializer
 */
class JsonSerializer implements SerializerInterface {

	public function serialize($data) {
		return json_encode($data);
	}

	public function unserialize($string) {
		return json_decode($string, TRUE);
	}

	public function getMimeType() {
		return 'application/json';
	}


}