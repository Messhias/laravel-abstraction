<?php
/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

trait GenericLogErrors
{
	/**
	 * Generic log error in the system into the system .logs
	 *
	 * @param mixed $exception
	 * @param mixed|null $message
	 *
	 * @param mixed $status
	 *
	 * @return JsonResponse
	 */
	public function logError(
		mixed $exception,
		mixed $message = null,
		mixed $status = 500
	): JsonResponse
	{
		Log::error($exception->getMessage(), [$exception]);
		
		if (!is_numeric($status)) {
			$status = 500;
		}
		
		/**
		 * If some http status code is greater 599 (which is the latest error code in HTTP list),
		 * we'll set it to 500 (that will trigger the error anyway) and help us out
		 * analyze the log.
		 */
		if ($status > 599) {
			$status = 500;
		}
		
		if ($status < 200) {
			$status = 500;
		}
		
		return Response::json([
			"success" => false,
			'error' => true,
			"data" => $exception,
			'message' => $message,
			"code" => $status,
		], $status);
	}
	
	
	/**
	 * Default found message.
	 *
	 * @return string
	 */
	protected function foundMessage(): string
	{
		return "Records found";
	}
	
	/**
	 * Default create message.
	 *
	 * @return string
	 */
	protected function createMessage(): string
	{
		return "New entry has been added";
	}
	
	/**
	 * Default update message.
	 *
	 * @return string
	 */
	protected function updateMessage(): string
	{
		return "The entry has been updated.";
	}
	
	/**
	 * Default deleted message.
	 *
	 * @return string
	 */
	protected function deletedMessage(): string
	{
		return "Entry has been deleted.";
	}
	
	/**
	 * @param string $customMessage
	 * @return JsonResponse
	 */
	protected function defaultKeyIdentifierError(string $customMessage = ""): JsonResponse
	{
		$msg = "Please provide the request key identifier.";
		
		if (!empty($customMessage)) {
			$msg = $customMessage;
		}
		
		return Response::json([
			"message" => $msg,
			"error" => true,
			"data" => false,
			"success" => false,
			"code" => 400,
		], 400);
	}
}