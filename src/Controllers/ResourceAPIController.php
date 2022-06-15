<?php
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
use Illuminate\Support\Facades\Response;
use Messhias\LaravelAbstraction\Repositories\RepositoryApiEloquent;
use Messhias\LaravelAbstraction\Traits\GenericLogErrors;
use Messhias\LaravelAbstraction\Interfaces\ResourceAPIController as RepositoryApiEloquentInterface;
use Messhias\LaravelAbstraction\Traits\ResourceAPI;

/**
 * Messhias\LaravelAbstraction\Controllers\ResourceAPIController
 *
 * @property RepositoryApiEloquent $repository
 * @mixin Controller
 */
abstract class ResourceAPIController extends Controller implements RepositoryApiEloquentInterface
{
	use GenericLogErrors;
	use ResourceAPI;
	
	protected RepositoryApiEloquent $repository;
	
	public function __construct(RepositoryApiEloquent $repository)
	{
		$this->setRepository($repository);
	}
	
	/**
	 * Set up the key identifier for the controller.
	 *
	 * @return string
	 */
	abstract protected function getKeyIdentifier(): string;
	
	/**
	 * Returns the repository representation.
	 *
	 * @return RepositoryApiEloquent
	 */
	public function getRepository(): RepositoryApiEloquent
	{
		return $this->repository;
	}
	
	/**
	 * Set up the repository into the abstract context.
	 *
	 * @param RepositoryApiEloquent $repository
	 */
	public function setRepository(RepositoryApiEloquent $repository): void
	{
		$this->repository = $repository;
	}
	
	/**
	 * @inheritDoc
	 */
	public function create(Request $request): JsonResponse
	{
		if (!$request->input($this->getKeyIdentifier())) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->repository->create(
					$request->input($this->getKeyIdentifier())
				)
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * Return the entity base on repository abstraction.
	 *
	 * @param mixed $id
	 *
	 * @return JsonResponse
	 */
	public function find(mixed $id): JsonResponse
	{
		if (empty($id)) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->find($id),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @return JsonResponse
	 */
	public function get(): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				unserialize(
					str_replace(array('NAN;', 'INF;'), '0;', serialize($this->getRepository()->get()))
				),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * Update entity base on id provided and database sent of the repository
	 * representation.
	 *
	 * @param Request $request
	 * @param mixed $id
	 *
	 * @return JsonResponse
	 * @throws Throwable
	 */
	public function update(Request $request, mixed $id): JsonResponse
	{
		if (empty($id)) {
			return $this->defaultKeyIdentifierError();
		}
		
		if (!$this->checkKeyIdentifier($this, $request)) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->update(
					$id,
					$request->input($this->getKeyIdentifier())
				)
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * Delete entity based on repository implementation
	 *
	 * @param mixed $id
	 *
	 * @return JsonResponse
	 */
	public function delete(mixed $id): JsonResponse
	{
		if (empty($id)) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->delete($id),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * Default request entries with pagination.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function paginate(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()
					->paginate($request),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @return JsonResponse
	 */
	public function active(): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->active(),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @return JsonResponse
	 */
	public function me(): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->me(),
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function login(Request $request): JsonResponse
	{
		if (!$request->input($this->getKeyIdentifier())) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this,
				// @phpstan-ignore-next-line
				$this->getRepository()->login(
					$request->input($this->getKeyIdentifier())
				)
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @return JsonResponse
	 */
	public function getFirstActive(): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->getFirstActive()
			);
		} catch (Exception $exception) {
			$code = (int)$exception->getCode();
			if ($code <= 0) {
				$code = 500;
			}
			
			return $this->logError($exception, "Something went wrong", $code);
		}
	}
	
	/**
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function filter(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()->filter($request),
			);
		} catch (Exception $exception) {
			return $this->logError($exception);
		}
	}
	
	/**
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function getWithRelationships(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()
					->getWithRelationships($request->all())
			);
		} catch (Exception $exception) {
			return $this->logError($exception);
		}
	}
	
	/**
	 * @param Request $request
	 * @param mixed $id
	 * @return JsonResponse
	 */
	public function findWithRelationships(Request $request, mixed $id): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this,
				$this->getRepository()
					->findWithRelationship($id, $request->all())
			);
		} catch (Exception $exception) {
			return $this->logError($exception);
		}
	}
	
	/**
	 * Set up a singular identifier for the class context process.
	 *
	 * @return string
	 */
	abstract protected function getSingularIdentifier(): string;
	
	/**
	 * Set up a plural identifier for the class context process.
	 */
	abstract protected function getPluralIdentifier(): string;
	
	/**
	 * @return JsonResponse
	 */
	public function defaultNotLoggedResponse(): JsonResponse
	{
		return Response::json([
			"data" => false,
			"message" => "Only logged in users can fetch this content.",
			"code" => 401,
			"success" => false,
			"error" => true,
			"completed_at" => Carbon::now()->format("Y-m-d H:m:s"),
		], 401);
	}
}