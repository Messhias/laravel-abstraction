<?php

declare(strict_types=1);

/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Interfaces;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

interface RepositoryAPIEloquent
{
    public function __construct();

    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param array|string $columns
     * @return Builder|Collection
     */
    public function get(array|string $columns = ['*']): Builder|Collection;

    /**
     * @param array $filter
     * @param array|string $columns
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function where(array $filter = [], array|string $columns = ["*"]): Collection|Builder;

    /**
     * @param array $data
     * @return Model|\Illuminate\Database\Eloquent\Builder
     */
    public function create(array $data): Model|\Illuminate\Database\Eloquent\Builder;

    /**
     * @param mixed $id
     * @param array|string $columns
     * @return \Illuminate\Database\Eloquent\Builder|Collection|Model|null
     */
    public function find(mixed $id, array|string $columns = ['*']): \Illuminate\Database\Eloquent\Builder|Collection|Model|null;

    /**
     * @param mixed $id
     * @param array $data
     * @return bool|int
     */
    public function update(mixed $id, array $data): bool|int;

    /**
     * @param mixed $id
     * @return mixed
     */
    public function delete(mixed $id): mixed;

    /**
     * @param array|string $columns
     * @return Model|static|null
     */
    public function first(array|string $columns = ['*']): Model|static|null;

    /**
     * @param int|Closure|null $perPage
     * @param array|string $columns
     * @param string $pageName
     * @param int|null $page
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function paginate(int|Closure|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', ?int $page = null): LengthAwarePaginator;

    /**
     * @param mixed $id
     * @param array $relationships
     * @return Model|Collection|\Illuminate\Database\Eloquent\Builder|array|null
     */
    public function findWithRelationship(mixed $id, array $relationships = []): Model|Collection|\Illuminate\Database\Eloquent\Builder|array|null;
}
