<?php

declare(strict_types=1);

/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Messhias\LaravelAbstraction\Repositories\RepositoryApiEloquent;

/**
 * @property RepositoryApiEloquent $repository
 *
 * @mixin Controller
 *
 * @internal
 */
abstract class ResourceAPIBaseController extends Controller
{
    protected RepositoryApiEloquent $repository;

    abstract public function __construct();

    /**
     * Set up the key identifier for the controller.
     *
     * @return string
     */
    abstract protected function getKeyIdentifier(): string;

    /**
     * Generic log error in the system into the system .logs
     *
     * @param Exception $exception
     * @param mixed|null $message
     *
     * @return JsonResponse
     */
    protected function logError(
		Exception $exception,
		mixed $message = null,
	): JsonResponse {
        Log::error($exception->getMessage(), [$exception]);

        return Response::json([
			'success' => false,
			'error' => true,
			'data' => $exception,
			'message' => $message,
			'code' => $exception->getCode(),
		], intval($exception->getCode()));
    }

    /**
     * @return JsonResponse
     */
    protected function defaultKeyIdentifierError(): JsonResponse
    {
        return Response::json([
			'message' => 'Please provide the request key identifier.',
			'error' => true,
			'data' => false,
			'success' => false,
			'code' => 400,
		], 400);
    }

    /**
     * @param mixed $data
     *
     * @return JsonResponse
     */
    protected function defaultJSONResponse(
		mixed $data
	): JsonResponse {
        return Response::json([
			'message' => $this->repository::getResponseMessage(),
			'data' => $data,
			'code' => $this->repository::getBodyCode(),
			'success' => $this->booleanResponses($this->repository),
			'error' => ! $this->booleanResponses($this->repository),
			'completed_at' => Carbon::now()->format('Y-m-d H:m:s'),
		], $this->repository::getResponseCode());
    }

    /**
     * Supportive trait function to treat the error, success
     * and any boolean results based on status code retrieve
     * by repository.
     *
     * @param RepositoryApiEloquent $repository
     *
     * @return bool
     */
    protected function booleanResponses(RepositoryApiEloquent $repository): bool
    {
        return match ($repository::getStatusResponse()) {
            201, 200 => true,
			default => false,
        };
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function checkKeyIdentifier(Request $request): bool
    {
        return $request->has($this->getKeyIdentifier());
    }
}
