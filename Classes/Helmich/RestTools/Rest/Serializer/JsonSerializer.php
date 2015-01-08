<?php
namespace Helmich\RestTools\Rest\Serializer;

class JsonSerializer implements SerializerInterface {

	public function serialize($data) {
		return json_encode($data);
	}

	public function unserialize($string) {
		return json_decode($string);
	}

	public function getMimeType() {
		return 'application/json';
	}


}