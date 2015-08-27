<?php
namespace Helmich\RestTools\Error;

use TYPO3\Flow\Error\AbstractExceptionHandler;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Property\Exception;
use TYPO3\Flow\Exception as FlowException;

/**
 * Special exception handler for a REST API in production context.
 *
 * This exception handler prints an uncaught exception as a JSON response. It
 * also attempts to guess an appropriate response code from the Exception type.
 *
 * @package Helmich\RestTools
 * @subpackage Error
 * @author Martin Helmich <typo3@martin-helmich.de>
 */
class ProductionRestExceptionHandler extends AbstractExceptionHandler {

	/**
	 * Echoes an exception for the web.
	 *
	 * @param \Exception $exception The exception
	 * @return void
	 */
	protected function echoExceptionWeb(\Exception $exception) {
		if ($exception instanceof Exception) {
			$statusCode = 400;
			$json = [
				'status' => 'invalid_request',
			];
		} elseif ($exception instanceof \TYPO3\Flow\Security\Exception) {
			$statusCode = 403;
			$json = [
				'status' => 'unauthorized',
			];
		} else {
			$statusCode = 500;
			if ($exception instanceof FlowException) {
				$statusCode = $exception->getStatusCode();
			}
			$json = [
				'status' => 'error',
			];
		}

		$statusMessage = Response::getStatusMessageByCode($statusCode);
		if (!headers_sent()) {
			header(sprintf('HTTP/1.1 %s %s', $statusCode, $statusMessage));
			header('Content-Type: application/json');
		}

		print(json_encode($json));
	}
}