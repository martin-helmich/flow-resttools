<?php
namespace Helmich\RestTools\Rest\Serializer;

interface SerializerInterface {

	public function serialize($data);

	public function unserialize($string);

	public function getMimeType();
}