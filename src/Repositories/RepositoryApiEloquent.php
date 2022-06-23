<?php

declare(strict_types=1);

namespace Messhias\LaravelAbstraction\Repositories;

use Closure;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Messhias\LaravelAbstraction\Interfaces\RepositoryAPIEloquent as RepositoryAPIEloquentInterface;

abstract class RepositoryApiEloquent implements RepositoryAPIEloquentInterface
{
	/**
	 * @var int
	 */
	public static int $statusResponse = 200;

	/**
	 * @var int
	 */
	public static int $bodyCode = 200;
	/**
	 * @var int
	 */
	protected static int $responseCode = 200;

	/**
	 * @var Model
	 */
	protected Model $model;

	/**
	 * @var string
	 */
	protected static string $responseMessage = 'Operation completed successfully.';

	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->makeModel();
	}

	/**
	 * @return int
	 */
	public static function getResponseCode(): int
	{
		return self::$responseCode;
	}

	/**
	 * @param int $responseCode
	 */
	public static function setResponseCode(int $responseCode): void
	{
		self::$responseCode = $responseCode;
	}

	/**
	 * @return int
	 */
	public static function getStatusResponse(): int
	{
		return self::$statusResponse;
	}

	/**
	 * @param int $statusResponse
	 */
	public static function setStatusResponse(int $statusResponse): void
	{
		self::$statusResponse = $statusResponse;
	}

	/**
	 * @return int
	 */
	public static function getBodyCode(): int
	{
		return self::$bodyCode;
	}

	/**
	 * @param int $bodyCode
	 */
	public static function setBodyCode(int $bodyCode): void
	{
		self::$bodyCode = $bodyCode;
	}

	/**
	 * @return Model
	 */
	public function getModel(): Model
	{
		return $this->model;
	}

	/**
	 * @param Model $model
	 */
	public function setModel(Model $model): void
	{
		$this->model = $model;
	}

	/**
	 * @return string
	 */
	public static function getResponseMessage(): string
	{
		return self::$responseMessage;
	}

	/**
	 * @param string $responseMessage
	 */
	public static function setResponseMessage(string $responseMessage): void
	{
		self::$responseMessage = $responseMessage;
	}

	/**
	 * @inheritdoc
	 */
	public function all(): Collection
	{
		self::setResponseCode(200);
		return $this->getModel()->all();
	}

	/**
	 * @inheritdoc
	 */
	public function get(array|string $columns = ['*']): Builder|Collection
	{
		self::setResponseCode(200);
		return $this->getModel()::query()->get($columns);
	}

	/**
	 * @inheritdoc
	 */
	public function where(array $filter = [], array|string $columns = ['*']): Builder|Collection
	{
		self::setResponseCode(200);
		return $this->getModel()::query()->where($filter)->get();
	}

	/**
	 * @inheritdoc
	 */
	public function find(mixed $id, array|string $columns = ['*']): \Illuminate\Database\Eloquent\Builder|Collection|Model|null
	{
		self::setResponseCode(200);
		return $this->getModel()::query()->find($id, $columns);
	}

	/**
	 * @inheritdoc
	 */
	public function create(array $data): Model|\Illuminate\Database\Eloquent\Builder
	{
		self::setResponseCode(201);
		return $this->getModel()::query()->create($data);
	}

	/**
	 * @inheritdoc
	 */
	public function update(mixed $id, array $data): bool|int
	{
		$object = $this->find($id);

		if ($object instanceof Model || $object instanceof Builder) {
			self::setResponseCode(200);
			return $object->update($data);
		}

		self::setResponseCode(400);
		return false;
	}

	/**
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function delete(mixed $id): mixed
	{
		$object = $this->find($id);

		if ($object instanceof Model || $object instanceof Builder) {
			self::setResponseCode(200);
			return $object->delete();
		}

		self::setResponseCode(400);
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function first(array|string $columns = ['*']): Model|static|null
	{
		self::setResponseCode(200);
		// @phpstan-ignore-next-line
		return $this->getModel()::query()->first($columns);
	}

	/**
	 * @inheritdoc
	 */
	public function paginate(int|Closure|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', ?int $page = null): LengthAwarePaginator
	{
		self::setResponseCode(200);
		return $this->getModel()::query()->paginate($perPage, $columns, $pageName, $page);
	}

	/**
	 * @param array $relationships
	 * @param string|Closure|null $callback
	 * @param array $where
	 * @param bool $paginate
	 *
	 * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
	 */
	public function getWithRelationships(array $relationships = [], string|Closure|null $callback = null, array $where = [], bool $paginate = false): Collection|LengthAwarePaginator|array
	{
		$model = $this->getModel();

		if (count($relationships) > 0) {
			$model = $model::query()->with($relationships, $callback);
		}

		if (count($where) > 0) {
			if ($model instanceof Builder) {
				$model = $model->where($where);
			}
		}

		if ($paginate) {
			if ($model instanceof Builder) {
				return $model->paginate();
			}
		}
		self::setResponseCode(200);

		// @phpstan-ignore-next-line
		return $model->get();
	}

	/**
	 * @inheritdoc
	 */
	public function findWithRelationship(mixed $id, array $relationships = []): Model|Collection|\Illuminate\Database\Eloquent\Builder|array|null
	{
		self::setResponseCode(200);

		return $this->getModel()::query()->with($relationships)->find($id);
	}

	abstract protected function model(): string;

	/**
	 * @return Model
	 */
	protected function makeModel(): Model
	{
		$this->setModel(new $this->model());

		return $this->getModel();
	}
}
