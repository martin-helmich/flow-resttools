<?php
namespace Helmich\RestTools\Rest\Serializer;


use Symfony\Component\Yaml\Yaml;

class YamlSerializer implements SerializerInterface {

	protected $inlineDepth = 4;

	protected $indentation = 2;

	public function serialize($data) {
		return Yaml::dump($data, $this->inlineDepth, $this->indentation);
	}

	public function unserialize($string) {
		return Yaml::parse($string);
	}

	public function getMimeType() {
		return 'application/yaml';
	}
}