<?php

declare(strict_types=1);

namespace Messhias\LaravelAbstraction\Repositories;

use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Messhias\LaravelAbstraction\Controllers\ResourceAPIController
 *
 * @property-static int $responseCode
 * @property Model $obj
 * @property Builder|Model $model
 * @property App $app
 */
abstract class RepositoryApiEloquent
//	implements RepositoryAPIEloquentInterface
{
	/**
	 * @var int
	 */
	protected static int $responseCode = 200;
	
	/**
	 * Model.
	 *
	 * @var Builder|Model
	 */
	protected Builder|Model $model;
	
	/**
	 * Application container.
	 *
	 * @var App
	 */
	protected App $app;
	
	/**
	 * Model object representation.
	 *
	 * @var Model
	 */
	protected Model $obj;
	
	/**
	 * RepositoryEloquent constructor.
	 *
	 * @param App $app
	 *
	 * @throws Exception
	 */
	public function __construct(App $app)
	{
		$this->setApp($app);
		$this->makeModel();
		self::setResponseCode(200);
	}
	
	/**
	 * @return Builder|Model
	 * @throws Exception
	 */
	protected function makeModel(): Model|Builder
	{
		$model = $this->getApp()->make($this->model());
		
		if (!$model instanceof Model) {
			throw new Exception("Class {$this->model()} must be an instance of Model");
		}
		
		$this->setModel($model);
		return $this->getModel();
	}
	
	/**
	 * Returning the application container.
	 *
	 * @return App
	 */
	public function getApp(): App
	{
		return $this->app;
	}
	
	/**
	 * Set up application container.
	 *
	 * @param App $app
	 */
	public function setApp(App $app): void
	{
		$this->app = $app;
	}
	
	/**
	 * Abstract set up model function.
	 *
	 * @return string
	 */
	abstract protected function model(): string;
	
	/**
	 * Returning the set-up model.
	 *
	 * @return Builder|Model
	 */
	public function getModel(): Builder|Model
	{
		return $this->model;
	}
	
	/**
	 * Set up the model into object context.
	 *
	 * @param Builder|Model $model
	 */
	public function setModel(Builder|Model $model): void
	{
		$this->model = $model;
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
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function all(): \Illuminate\Database\Eloquent\Collection
	{
		self::setResponseCode(200);
		return $this->getModel()->all();
	}
	
	/**
	 * @return Collection
	 */
	public function get()
	{
		self::setResponseCode(200);
		return $this->getModel()->get();
	}
	
	/**
	 * @param array $filter
	 * @return Collection
	 */
	public function where(array $filter = [])
	{
		self::setResponseCode(200);
		return $this->getModel()->where($filter)->get();
	}
	
	/**
	 * @param array $data
	 * @return mixed
	 */
	public function create(array $data = [])
	{
		$this->obj = new $this->model();
		
		return DB::transaction(function () use ($data) {
			return $this->saveObj(data: $data, creating: true);
		});
	}
	
	/**
	 * @param array $data
	 * @param bool $creating
	 * @param bool $deleting
	 * @param bool $updating
	 * @return Model|bool
	 */
	protected function saveObj(
		array $data = [],
		bool  $creating = false,
		bool  $deleting = false,
		bool  $updating = false,
	): Model|bool
	{
		$this->syncData($data);
		
		if ($this->obj->save()) {
			$data = $this->afterSave(
				model: $this->obj,
				data: $data,
				creating: $creating,
				updating: $updating,
				deleting: $deleting
			);
			if ($creating) {
				self::setResponseCode(201);
			} else {
				self::setResponseCode(200);
			}
			return $data;
		}
		
		return false;
	}
	
	/**
	 * Sync object model data.
	 *
	 * @param array $data
	 * @return void
	 */
	protected function syncData(array $data = []): void
	{
		if (!$this->obj) {
			return;
		}
		
		$this->obj->setRawAttributes($this->getDefaultData());
		$fields = $this->model->getFillable();
		
		array_map(function ($field) use ($data) {
			if (array_key_exists($field, $data)) {
				$this->obj->{$field} = $data[$field];
			}
		}, $fields);
	}
	
	/**
	 * @return mixed
	 */
	protected function getDefaultData()
	{
		return $this->obj->getAttributes();
	}
	
	/**
	 * @param Model $model
	 * @return Model
	 */
	protected function beforeSave(Model $model): Model
	{
		return $model;
	}
	
	/**
	 * After save returning the object sync data of collections instances.
	 *
	 * In this method we'll sync all the relationships in the case of the request
	 * full the models relationships instances.
	 *
	 * For example, the model XPTO has the relationship x and in the request instance the
	 * x method is filled it'll synced automatically to the model data.
	 *
	 * Evolution of ORM relationship.
	 *
	 * @param Builder|Model $model
	 * @param array $data
	 * @param bool $creating
	 * @param bool $updating
	 * @param bool $deleting
	 * @return Builder|Model
	 */
	protected function afterSave(
		Builder|Model $model,
		array         $data,
		bool          $creating = false,
		bool          $updating = false,
		bool          $deleting = false,
	): Builder|Model
	{
		$relationships = [];
		foreach ($model->getRelations() as $r => $relationship) {
			if (array_key_exists($r, $data)) {
				$relationships[$r] = $data[$r];
			}
		}
		
		
		if (count($relationships) > 0) {
			foreach ($relationships as $key => $d) {
				$model->$key()->sync(
					data: $d,
					create: $creating,
					update: $updating,
					deleting: $deleting,
				);
			}
			
			if ($creating) {
				$model->save();
			} elseif ($updating) {
				$model->update();
			}
		}
		
		return $model;
	}
	
	/**
	 * @param mixed $id
	 * @param array $data
	 * @return bool|Model
	 */
	public function update(mixed $id, array $data = []): Model|bool
	{
		$this->obj = $this->find($id);
		
		$save_data = false;
		
		if (!empty($this->obj)) {
			self::setResponseCode(200);
			DB::beginTransaction();
			try {
				$save_data = $this->saveObj(
					data: $data,
					updating: true,
				);
				DB::commit();
			} catch (Exception $e) {
				Log::error($e->getMessage(), [$e]);
				DB::rollBack();
				
				return false;
			}
		}
		
		if (!$save_data) {
			self::setResponseCode(400);
		}
		
		return $save_data;
	}
	
	/**
	 * @param mixed $id
	 * @return array|Builder|mixed
	 */
	public function find(mixed $id)
	{
		self::setResponseCode(200);
		$this->obj = $this->getModel()->find($id);
		
		if (!$this->obj) {
			return [];
		}
		
		return $this->obj;
	}
	
	/**
	 * @param mixed $id
	 * @param array $relationships
	 * @return mixed
	 */
	public function findWithRelationship(mixed $id, array $relationships = [])
	{
		self::setResponseCode(200);
		return $this->getModel()->with($relationships)->find($id);
	}
	
	/**
	 * @param mixed $id
	 * @return bool|int
	 */
	public function delete(mixed $id): bool|int
	{
		$obj = $this->find($id);
		
		if (!is_array($obj)) {
			DB::beginTransaction();
			try {
				if ($obj->active) {
					$obj->active = false;
					$obj->save();
					$obj->refresh();
				}
				$delete = $obj->delete();
				DB::commit();
				
				return $delete;
			} catch (Exception $exception) {
				Log::error($exception->getMessage(), [$exception]);
				DB::rollBack();
				
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @return Collection
	 */
	public function active()
	{
		self::setResponseCode(200);
		return $this->getModel()->where('active', "true")->get();
	}
	
	/**
	 * @return LengthAwarePaginator
	 */
	public function activeWithPagination()
	{
		self::setResponseCode(200);
		return $this->getModel()->where('active', "true")->paginate();
	}
	
	/**
	 * @param array|Request $filter
	 * @return Collection
	 */
	public function filter(array|Request $filter)
	{
		self::setResponseCode(200);
		return $this->getModel()->where($filter)->get();
	}
	
	/**
	 * @param array $filter
	 * @return Model|Builder|null
	 */
	public function filterOne(array $filter): Model|Builder|null
	{
		self::setResponseCode(200);
		return $this->model->where($filter)->first();
	}
	
	/**
	 * @return Builder|Model|object|null
	 */
	public function first()
	{
		self::setResponseCode(200);
		return $this->getModel()
			->first();
	}
	
	/**
	 * @return LengthAwarePaginator
	 */
	public function withPagination()
	{
		self::setResponseCode(200);
		return $this->getModel()
			->paginate();
	}
	
	/**
	 * @return Builder|Model|object|null
	 */
	public function getFirstActive()
	{
		self::setResponseCode(200);
		return $this->getModel()
			->where("active", "true")
			->first();
	}
	
	/**
	 * @param Request $request
	 * @return LengthAwarePaginator
	 */
	public function paginate(Request $request)
	{
		self::setResponseCode(200);
		return $this->getModel()
			->paginate($request);
	}
	
	/**
	 * @param array $relationships
	 * @param array $where
	 * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection<Model, ArrayObject>|Collection<Model, ArrayObject>|
	 */
	public function getWithRelationships(array $relationships = [], array $where = []): \Illuminate\Database\Eloquent\Collection|Collection|array
	{
		self::setResponseCode(200);
		$data = $this->getModel();
		
		if (count($relationships) > 0) {
			$data = $this->getModel()
				->with($relationships);
		}
		
		if (count($where) > 0) {
			$data = $data->where($where);
		}
		
		return $data->get();
	}
}
