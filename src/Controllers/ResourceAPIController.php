<?php
/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Messhias\LaravelAbstraction\Interfaces\ResourceAPIController as RepositoryApiEloquentInterface;
use Messhias\LaravelAbstraction\Repositories\RepositoryApiEloquent;
use Messhias\LaravelAbstraction\Traits\GenericLogErrors;
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

    abstract public function __construct(RepositoryApiEloquent $repository);

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
                    (array)$request->input($this->getKeyIdentifier())
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
     * @param Request $request
     * @param mixed $id
     * @return JsonResponse
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
                    (array)$request->input($this->getKeyIdentifier())
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
                    ->paginate(
                        intval($request->query("perPage", "10")),
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
     * Set up a plural identifier for the class context process.
     */
    abstract protected function getPluralIdentifier(): string;
}
