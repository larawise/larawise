<?php

namespace Larawise\Repository;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Larawise\Contracts\RepositoryContract;
use Larawise\Database\Eloquent\Builder;
use Larawise\Models\Model;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
abstract class Repository implements RepositoryContract
{
    /**
     * @var Model|Builder|EloquentBuilder|EloquentModel
     */
    protected $original;

    /**
     * @var Model|Builder|EloquentBuilder|EloquentModel
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param Model|Builder|EloquentBuilder|EloquentModel $model
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->original = $model;
        $this->model = $model;
    }

    /**
     * @return Model|Builder|EloquentBuilder|EloquentModel
     */
    public function model()
    {
        return new $this->original();
    }

    /**
     * @param Model|Builder|EloquentBuilder|EloquentModel $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Returns the current model instance, optionally eager-loading relationships.
     *
     * @param array $with
     *
     * @return EloquentBuilder
     */
    public function make($with = [])
    {
        // Apply eager-loading if relationships are provided
        if (! empty($with)) {
            $this->model = $this->model->with($with);
        }

        // Return the current model builder
        return $this->model;
    }

    /**
     * Returns the database table name associated with the current model.
     *
     * @return string
     */
    public function table()
    {
        // If model is wrapped in a query builder, extract the underlying model
        if ($this->model instanceof Builder) {
            return $this->model->getModel()->getTable();
        }

        // Otherwise return the table name directly from the model
        return $this->model->getTable();
    }

    /**
     * Resets the internal model instance to its original state.
     *
     * @return self
     */
    public function reset()
    {
        // Reinitialize the model to its original class
        $this->model = new $this->original();

        return $this;
    }

    /**
     * Retrieves a single record by its primary key (`id`), with optional eager-loaded relationships.
     *
     * @param int|string $id
     * @param array $with
     *
     * @return EloquentModel
     */
    public function findById($id, $with = [])
    {
        // Apply eager-loading if relationships are provided
        $data = $this->make($with)->where('id', $id);

        // Execute query and return the first result
        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    /**
     * Retrieves a single record by its primary key (`id`) or throws a `ModelNotFoundException` if not found.
     *
     * @param int|string $id
     * @param array $with
     *
     * @return EloquentModel
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, array $with = [])
    {
        // Apply eager-loading if relationships are provided
        $data = $this->make($with)->where('id', $id);

        // Execute query and return the first result
        $result = $this->applyBeforeExecuteQuery($data, true)->first();

        // If a record is found, return it
        if (! empty($result)) {
            return $result;
        }

        // Resolve model class for exception context
        $model = $this->model();
        if ($model instanceof Builder) {
            $model = $model->getModel();
        }

        // Throw exception if no record is found
        throw (new ModelNotFoundException())->setModel($model::class, $id);
    }

    /**
     * Applies contextual filters to the query before execution, based on admin or frontend context.
     *
     * @param EloquentBuilder|\Illuminate\Database\Query\Builder $data
     * @param bool $isSingle
     *
     * @return EloquentBuilder|\Illuminate\Database\Query\Builder
     */
    public function applyBeforeExecuteQuery($data, $isSingle = false)
    {
        // Determine filter constant based on context and query type
        $filter = $isSingle
            ? SRYLIUS_FILTER_BEFORE_GET_APP_SINGLE
            : SRYLIUS_FILTER_BEFORE_GET_APP_ITEM;

        // Override filter if in admin context
        if (Srylius::isInAdmin()) {
            $filter = $isSingle
                ? SRYLIUS_FILTER_BEFORE_GET_SINGLE
                : SRYLIUS_FILTER_BEFORE_GET_ITEM;
        }

        // Apply contextual filter to the query
        $data = apply_filters($filter, $data, $this->original);

        // Reset internal query state to avoid side effects
        $this->reset();

        return $data;
    }

    /**
     * Retrieves all records from the model, optionally eager-loading relationships.
     *
     * @param array $with
     *
     * @return Collection
     */
    public function all($with = [])
    {
        // Apply eager-loading if relationships are provided
        $data = $this->make($with);

        // Execute query and return all results
        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * Retrieves all records matching the given condition, with optional column selection and eager-loaded relationships.
     *
     * @param array $condition
     * @param array $with
     * @param array $select
     *
     * @return Collection
     */
    public function allBy($condition = [], $with = [], $select = ['*'])
    {
        // Apply filtering conditions
        $this->apply($condition);

        // Apply eager-loading and column selection
        $data = $this->make($with)->select($select);

        // Execute query and return results
        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * Creates a new record or updates an existing one based on the given condition.
     *
     * @param array|EloquentModel $data
     * @param array $condition
     *
     * @return EloquentModel|bool
     */
    public function createOrUpdate($data, $condition = [])
    {
        // Handle array input: create or update based on condition
        if (is_array($data)) {
            // If no condition, create a fresh model
            if (empty($condition)) {
                $item = new $this->original();
            } else {
                // Try to find existing record
                $item = $this->firstBy($condition);
            }

            // If no match found, create a new model
            if (empty($item)) {
                $item = new $this->original();
            }

            // Fill model with incoming data
            $item = $item->fill($data);
        }
        // If input is already a model, use it directly
        elseif ($data instanceof EloquentModel) {
            $item = $data;
        }
        // Invalid input type
        else {
            return false;
        }

        // Reset internal query state
        $this->reset();

        // Save and return model if successful
        if ($item->save()) {
            return $item;
        }

        return false;
    }

    /**
     * Retrieves the first record matching the given condition, with optional column selection and eager-loaded relationships.
     *
     * @param array $condition
     * @param array $select
     * @param array $with
     *
     * @return Model|Builder|EloquentBuilder|EloquentModel
     */
    public function firstBy($condition = [], $select = ['*'], $with = [])
    {
        // Reset internal query state
        $this->reset();

        // Apply eager-loading if relationships are provided
        $this->make($with);

        // Apply filtering conditions
        $this->apply($condition);

        // Select specified columns or fallback to all
        $data = ! empty($select)
            ? $this->model->select($select)
            : $this->model->select('*');

        // Execute query and return the first result
        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    /**
     * Retrieves the first record matching the given condition, including soft-deleted entries.
     *
     * @param array $condition
     * @param array $select
     *
     * @return Model|Builder|EloquentBuilder|EloquentModel
     */
    public function firstByWithTrash($condition = [], $select = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Include soft-deleted records
        $query = $this->model->withTrashed();

        // Apply column selection if provided
        if (! empty($select)) {
            return $query->select($select)->first();
        }

        // Apply final query transformations and return first result
        return $this->applyBeforeExecuteQuery($query, true)->first();
    }

    /**
     * Creates a new model record with the given data.
     *
     * @param array $data
     *
     * @return Model|Builder|EloquentBuilder|EloquentModel
     */
    public function create($data)
    {
        // Create and persist the model with given attributes
        $data = $this->model->create($data);

        // Reset internal query state to avoid side effects
        $this->reset();

        return $data;
    }

    /**
     * Inserts one or more records into the database in bulk.
     *
     * @param array $data
     *
     * @return bool
     */
    public function insert($data)
    {
        // Perform bulk insert directly on the model's table
        return $this->model->insert($data);
    }

    /**
     * Archives all records matching the given condition.
     *
     * @param array $condition
     *
     * @return bool|int
     */
    public function archiveBy($condition)
    {
        return $this->updateBy($condition, [
            'archived_at' => $this->model->fromDateTime($this->model->freshTimestamp())
        ]);
    }

    /**
     * Updates records matching the given condition with the provided data.
     *
     * @param array $condition
     * @param array $data
     *
     * @return bool|int
     */
    public function updateBy($condition, $data)
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Execute the update and capture affected row count
        $data = $this->model->update($data);

        // Reset internal query state to avoid side effects
        $this->reset();

        return $data;
    }

    /**
     * Applies conditions and prepares a select query on the model.
     *
     * @param array $select
     * @param array $condition
     *
     * @return mixed
     */
    public function selectBy($select = ['*'], $condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Build the select query with specified columns
        $data = $this->model->select($select);

        // Apply any final query transformations (e.g. pagination, sorting)
        return $this->applyBeforeExecuteQuery($data);
    }

    /**
     * Deletes all records matching the given condition.
     *
     * @param array $condition
     *
     * @return bool
     */
    public function deleteBy($condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Retrieve matching records
        $data = $this->model->get();

        // If no records found, return false
        if ($data->isEmpty()) {
            return false;
        }

        // Delete each record individually
        foreach ($data as $item) {
            $item->delete();
        }

        // Reset internal query state to avoid side effects
        $this->reset();

        return true;
    }

    /**
     * Permanently deletes the first soft-deleted or active record matching the given condition.
     *
     * @param array $condition
     *
     * @return void
     */
    public function forceDeleteBy($condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Retrieve the first matching record, including soft-deleted ones
        $item = $this->model->withTrashed()->first();

        // If a record is found, force delete it permanently
        if (! empty($item)) {
            $item->forceDelete();
        }

        // Reset internal query state to avoid side effects
        $this->reset();
    }

    /**
     * Counts the number of records matching the given condition.
     *
     * @param array $condition
     *
     * @return int
     */
    public function countBy($condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Execute count query and capture result
        $data = $this->model->count();

        // Reset internal query state to avoid side effects
        $this->reset();

        return $data;
    }

    /**
     * Retrieves a flat array of values from a single column, optionally keyed by another column.
     *
     * @param string $column
     * @param string $key
     * @param array $condition
     *
     * @return array
     */
    public function pluckBy($column, $key = null, $condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Select only the necessary columns
        $select = [$column];
        if (! empty($key)) {
            $select = [$column, $key];
        }

        // Build the query
        $data = $this->model->select($select);

        // Execute and transform the result into a flat array
        return $this->applyBeforeExecuteQuery($data)->pluck($column, $key)->all();
    }

    /**
     * Restores the first soft-deleted record matching the given condition.
     *
     * @param array $condition
     *
     * @return void
     */
    public function restoreBy($condition = [])
    {
        // Apply filtering conditions to the query
        $this->apply($condition);

        // Retrieve the first matching record, including soft-deleted ones
        $item = $this->model->withTrashed()->first();

        // If a trashed record is found, restore it
        if (! empty($item)) {
            $item->restore();
        }

        // Reset internal query state to avoid side effects
        $this->reset();
    }

    /**
     * Retrieves a collection of records based on flexible query parameters.
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function get($parameters = [])
    {
        // Merge defaults with incoming parameters
        $parameters = array_merge([
            'condition' => [],
            'order_by' => [],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => ['*'],
            'with' => [],
            'withCount' => [],
            'withAvg' => [],
        ], $parameters);

        // Apply filtering conditions
        $this->apply($parameters['condition']);

        $data = $this->model;

        // Apply column selection
        if ($parameters['select']) {
            $data = $data->select($parameters['select']);
        }

        // Apply ordering
        foreach ($parameters['order_by'] as $column => $direction) {
            if (! in_array(strtolower($direction), ['asc', 'desc'])) {
                continue;
            }

            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }

        // Apply eager-loading
        if (! empty($parameters['with'])) {
            $data = $data->with($parameters['with']);
        }

        // Apply relationship counts
        if (! empty($parameters['withCount'])) {
            $data = $data->withCount($parameters['withCount']);
        }

        // Apply average aggregation
        if (! empty($parameters['withAvg'])) {
            $data = $data->withAvg($parameters['withAvg'][0], $parameters['withAvg'][1]);
        }

        // Handle result type: single, limited, paginated, or full
        if ($parameters['take'] == 1) {
            $result = $this->applyBeforeExecuteQuery($data, true)->first();
        } elseif ($parameters['take'] && $parameters['take'] > 0) {
            $result = $this->applyBeforeExecuteQuery($data)->take((int) $parameters['take'])->get();
        } elseif ($parameters['paginate']['per_page']) {
            $paginateType = 'paginate';

            // Use custom pagination method if defined
            if (Arr::get($parameters, 'paginate.type') && method_exists($data, Arr::get($parameters, 'paginate.type'))) {
                $paginateType = Arr::get($parameters, 'paginate.type');
            }

            // Resolve original model for key naming
            $original = $this->original instanceof Builder ? $this->original->getModel() : $this->original;

            $perPage = (int) Arr::get($parameters, 'paginate.per_page') ?: 10;
            $pageName = Arr::get($parameters, 'paginate.page_name', 'page');
            $currentPage = (int) Arr::get($parameters, 'paginate.current_paged', 1) ?: 1;

            $result = $this->applyBeforeExecuteQuery($data)
                ->$paginateType(
                    $perPage > 0 ? $perPage : 10,
                    [$original->getTable() . 'Eloquent' . $original->getKeyName()],
                    $pageName,
                    $currentPage > 0 ? $currentPage : 1
                );
        } else {
            $result = $this->applyBeforeExecuteQuery($data)->get();
        }

        return $result;
    }

    /**
     * Retrieves records where a column matches any value in the given array, with optional filters, limits, or pagination.
     *
     * @param string $column
     * @param array $value
     * @param array $arguments
     *
     * @return Collection|LengthAwarePaginator
     */
    public function whereInBy($column, array $value = [], array $arguments = [])
    {
        // Apply whereIn filter
        $data = $this->model->whereIn($column, $value);

        // Apply additional conditions if provided
        if (! empty(Arr::get($arguments, 'where'))) {
            $this->apply($arguments['where']);
        }

        // Apply final query transformations
        $data = $this->applyBeforeExecuteQuery($data);

        // Handle pagination
        if (! empty(Arr::get($arguments, 'paginate'))) {
            return $data->paginate((int) $arguments['paginate']);
        }

        // Handle limit
        if (! empty(Arr::get($arguments, 'limit'))) {
            return $data->limit((int) $arguments['limit']);
        }

        // Default: return full result set
        return $data->get();
    }

    /**
     * Retrieves the first matching model or creates a new one with the given attributes.
     *
     * @param array $data
     * @param array $with
     *
     * @return Model
     */
    public function firstOrCreate($data, $with = [])
    {
        // Attempt to find or create the model
        $data = $this->model->firstOrCreate($data, $with);

        // Reset internal query state to avoid side effects
        $this->reset();

        return $data;
    }

    /**
     * Attempts to retrieve the first matching model or returns a new instance.
     *
     * @param array $condition
     *
     * @return Model
     */
    public function firstOrNew($condition)
    {
        // Apply query conditions to the model builder
        $this->apply($condition);

        // Attempt to fetch the first matching record
        $result = $this->model->first() ?: new $this->original;

        // Reset internal query state to avoid side effects
        $this->reset();

        return $result;
    }

    /**
     * Dynamically applies a set of conditions to the given model instance.
     *
     * @param array $where
     * @param EloquentBuilder|null $model
     *
     * @return void
     */
    protected function apply(array $where, &$model = null)
    {
        // Use provided model or fallback to internal one
        $newModel = $model ?: $this->model;

        foreach ($where as $field => $value) {
            // If value is a closure, apply it directly to the query
            if ($value instanceof Closure) {
                $newModel = $value($newModel);
                continue;
            }

            // If value is an array, treat it as [field, operator, value]
            if (is_array($value)) {
                [$field, $condition, $val] = $value;

                // Normalize condition and apply appropriate query method
                $newModel = match (strtoupper($condition)) {
                    'IN' => $newModel->whereIn($field, $val),
                    'NOT_IN' => $newModel->whereNotIn($field, $val),
                    default => $newModel->where($field, $condition, $val),
                };
            } else {
                // Apply simple equality condition
                $newModel = $newModel->where($field, $value);
            }
        }

        // Persist the mutated query builder
        if (! $model) {
            $this->model = $newModel;
        } else {
            $model = $newModel;
        }
    }
}
