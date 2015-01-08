<?php
namespace Helmich\RestTools\Rest\Serializer;

class MsgpackSerializer implements SerializerInterface {

	public function serialize($data) {
		if (!function_exists('msgpack_pack')) {
			throw new \Exception('msgpack extension must be installed!');
		}
		return msgpack_pack($data);
	}

	public function unserialize($string) {
		if (!function_exists('msgpack_unpack')) {
			throw new \Exception('msgpack extension must be installed!');
		}
		return msgpack_unpack($string);
	}

	public function getMimeType() {
		return 'application/x-msgpack';
	}
}