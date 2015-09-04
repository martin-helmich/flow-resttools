<?php
namespace Helmich\RestTools\Exception;

use TYPO3\Flow\Exception as FlowException;

class BadContentTypeException extends FlowException {

	protected $statusCode = 415;

}