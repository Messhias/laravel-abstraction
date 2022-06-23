<?php

declare(strict_types=1);

/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Messhias\LaravelAbstraction\Interfaces\ResourceAPIController as RepositoryApiEloquentInterface;

abstract class ResourceAPIController extends ResourceAPIBaseController implements RepositoryApiEloquentInterface
{
	/**
	 * @inheritDoc
	 */
	public function create(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this->repository->create(
					(array)$request->input($this->getKeyIdentifier())
				)
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
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
				$this->repository->find($id),
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
		}
	}
	
	/**
	 * @return JsonResponse
	 */
	public function get(): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				unserialize(
					str_replace(['NAN;', 'INF;'], '0;', serialize($this->repository->get()))
				),
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
		}
	}
	
	/**
	 * @param Request $request
	 * @param mixed $id
	 *
	 * @return JsonResponse
	 */
	public function update(Request $request, mixed $id): JsonResponse
	{
		if (!$this->checkKeyIdentifier($request)) {
			return $this->defaultKeyIdentifierError();
		}
		
		try {
			return $this->defaultJSONResponse(
				$this->repository->update(
					$id,
					(array)$request->input($this->getKeyIdentifier())
				)
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
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
		try {
			return $this->defaultJSONResponse(
				$this->repository->delete($id),
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
		}
	}
	
	/**
	 * Default request entries with pagination.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function paginate(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this->repository
					->paginate(
						intval($request->query('perPage', '10')),
					),
			);
		} catch (Exception $exception) {
			return $this->logError($exception, 'Something went wrong');
		}
	}
	
	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function getWithRelationships(Request $request): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this->repository
					->getWithRelationships($request->all())
			);
		} catch (Exception $exception) {
			return $this->logError($exception);
		}
	}
	
	/**
	 * @param Request $request
	 * @param mixed $id
	 *
	 * @return JsonResponse
	 */
	public function findWithRelationships(Request $request, mixed $id): JsonResponse
	{
		try {
			return $this->defaultJSONResponse(
				$this->repository
					->findWithRelationship($id, $request->all())
			);
		} catch (Exception $exception) {
			return $this->logError($exception);
		}
	}
}
