<?php
namespace Helmich\RestTools\Rest\Serializer;

use Helmich\RestTools\Exception\BadContentTypeException;

class AutoSerializer implements SerializerInterface {

	private $typeMapping = [
		'application/json' => JsonSerializer::class,
		'application/yaml' => YamlSerializer::class,
		'application/x-msgpack' => MsgpackSerializer::class
	];

	/**
	 * @var SerializerInterface
	 */
	private $serializer;

	public function __construct($mimeType) {
		if (isset($this->typeMapping[$mimeType])) {
			$this->serializer = new $this->typeMapping[$mimeType]();
		} else {
			throw new BadContentTypeException('No serializer known for MIME type "' . $mimeType . '"!');
		}
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data) {
		return $this->serializer->serialize($data);
	}

	/**
	 * @param string $string
	 * @return mixed
	 */
	public function unserialize($string) {
		return $this->serializer->unserialize($string);
	}

	/**
	 * Gets the IANA media typt of the serialized format.
	 *
	 * @return string
	 */
	public function getMimeType() {
		return $this->serializer->getMimeType();
	}
}