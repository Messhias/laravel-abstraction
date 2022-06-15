<?php

declare(strict_types=1);

/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

namespace Messhias\LaravelAbstraction\Interfaces;

use Illuminate\Http\Request;

interface RepositoryAPIEloquent
{
	/**
	 * Find an entry in the model.
	 */
	public function find(mixed $id);
	
	/**
	 * Retrieve all the information from the model.
	 */
	public function all();
	
	/**
	 * Retrieve all the information from the model.
	 */
	public function get();
	
	/**
	 * Create a new entry in the model.
	 *
	 * @param array $data
	 */
	public function create(array $data);
	
	/**
	 * @param $id
	 * @param array $data
	 * @return mixed
	 */
	public function update($id, array $data);
	
	/**
	 * Delete an information from the model.
	 */
	public function delete(mixed $id);
	
	/**
	 * @param array $relationships
	 * @param array $where
	 */
	public function getWithRelationships(
		array $relationships = [],
		array $where = []
	): mixed;
	
	public function withPagination();
	
	public function first();
	
	public function filterOne(array $filter);
	
	public function filter(array|Request $filter);
	
	public function validate(): bool;
}
