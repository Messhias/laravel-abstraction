<?php

declare(strict_types=1);

/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ResourceAPIController
{
    /**
     * Create new entity based on repository abstraction.
     */
    public function create(Request $request): JsonResponse;

    /**
     * Return the entity base on repository abstraction.
     *
     * @info
     * The id is a mixed because by default we're using MySQL database but you can
     * remove the id type identifier and left the auto casting of PHP work for you.
     */
    public function find(mixed $id): JsonResponse;

    /**
     * Return all the entities base on repository abstraction.
     */
    public function get(): JsonResponse;

    /**
     * Update entity base on id provided and database sent of the repository
     * representation.
     */
    public function update(Request $request, mixed $id): JsonResponse;

    /**
     * Delete entity based on repository implementation
     */
    public function delete(mixed $id): JsonResponse;
}
