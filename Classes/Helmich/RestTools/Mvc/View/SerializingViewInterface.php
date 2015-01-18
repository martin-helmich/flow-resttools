<?php
namespace Helmich\RestTools\Mvc\View;


use Helmich\RestTools\Rest\Normalizer\NormalizerInterface;

interface SerializingViewInterface {

	public function registerNormalizerForClass($objectClass, NormalizerInterface $normalizer);

	public function registerFallbackNormalizer(NormalizerInterface $normalizer);

}