<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class MySqlSearchEngine extends Engine
{
	/**
	 * Create a new engine instance.
	 *
	 */
	public function __construct()
	{

	}

	/**
	 * Update the given model in the index.
	 *
	 * @param Collection $models
	 *
	 * @return void
	 */
	public function update($models)
	{
		//
	}

	/**
	 * Remove the given model from the index.
	 *
	 * @param Collection $models
	 *
	 * @return void
	 */
	public function delete($models)
	{
		//
	}

	/**
	 * Perform the given search on the engine.
	 *
	 * @param Builder $builder
	 *
	 * @return mixed
	 */
	public function search(Builder $builder)
	{
		$results = $this->performSearch($builder);
		return $results->get();
	}

	/**
	 * Perform the given search on the engine.
	 *
	 * @param Builder $builder
	 * @param int     $perPage
	 * @param int     $page
	 *
	 * @return mixed
	 */
	public function paginate(Builder $builder, $perPage, $page)
	{
		$results = $this->performSearch($builder);
		return $results->get();
	}

	/**
	 * Perform the given search on the engine.
	 *
	 * @param Builder $builder
	 *
	 * @return mixed
	 */
	protected function performSearch(Builder $builder)
	{
		if(count($builder->model->searchable) < 1)
			return [];

		$query = $builder->model::where("id", 0);
		foreach($builder->model->searchable as $col)
		{
			$query = $query->orWhere($col, "like", "%{$builder->query}%");
		}

		return $query;
	}

	/**
	 * Map the given results to instances of the given model.
	 *
	 * @param mixed                               $results
	 * @param \Illuminate\Database\Eloquent\Model $model
	 *
	 * @return Collection
	 */
	public function map($results, $model)
	{
		return $results;
	}

	/**
	 * Get the total count from a raw result returned by the engine.
	 *
	 * @param mixed $results
	 *
	 * @return int
	 */
	public function getTotalCount($results)
	{
		return count($results);
	}
}
