<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

trait HasList
{
  /**
   * Основной метод построения запроса
   */
  protected static function buildBaseQuery(array $params = [], array $with = []): Builder
  {
    $query = static::query()->with($with);
    $params = self::validateParams($params);
    $search = Arr::get($params, 'search');

    if ($search) {
      self::applySearch($query, $search);
    }

    self::applySorting($query, $params);

    return $query;
  }

  /**
   * Применение условий поиска
   */
  protected static function applySearch(Builder $query, string $search): void
  {
    $query->where(function (Builder $q) use ($search) {
      if (property_exists(static::class, 'searchConfig')) {
        $config = (new static)->searchConfig;

        // Поиск по связанной модели
        if ($config['relation'] ?? false) {
          $q->whereHas($config['relation'], function (Builder $linkQuery) use ($search, $config) {
            self::applyRelationSearch($linkQuery, $search, $config);
          });
        }

        // Поиск по полям основной модели
        if ($config['model_fields'] ?? false) {
          $q->orWhere(function (Builder $modelQuery) use ($search, $config) {
            foreach ($config['model_fields'] as $field) {
              $modelQuery->orWhere($field, 'LIKE', "%{$search}%");
            }
          });
        }
      } else {
        // Дефолтный поиск
        $q->where('name', 'LIKE', "%{$search}%");
      }
    });
  }

  /**
   * Поиск по связанной модели
   */
  protected static function applyRelationSearch(Builder $query, string $search, array $config): void
  {
    $query->where(function (Builder $q) use ($search, $config) {
      $fields = $config['relation_fields'] ?? [];
      $useFullText = $config['full_text'] ?? false;
      $mode = $config['full_text_mode'] ?? null;

      if ($useFullText) {
        $options = [];
        if ($mode) {
          $options['mode'] = $mode;
          $search = preg_replace('/(\w+)/u', '$1*', $search); // Поддержка Unicode
        }
        $q->whereFullText($fields, $search, $options);
      } else {
        foreach ($fields as $field) {
          $q->orWhere($field, 'LIKE', "%{$search}%");
        }
      }
    });
  }


  protected static function applyRelationSort(Builder $query, string $column, string $direction): void
  {
    [$relationName, $relationColumn] = explode('.', $column, 2);
    $model = $query->getModel();

    if (!method_exists($model, $relationName)) {
      throw new \InvalidArgumentException("Relation {$relationName} not found");
    }

    $relation = $model->{$relationName}();
    $relatedModel = $relation->getRelated();

    if ($relation instanceof MorphOne) {
      $typeColumn = $relation->getMorphType(); // Например: "linkable_type"
      $idColumn = $relation->getForeignKeyName(); // Например: "linkable_id"

      $subQuery = $relatedModel->select($relationColumn)
        ->whereColumn(
          "{$relatedModel->getTable()}.{$idColumn}",
          "{$model->getTable()}.{$model->getKeyName()}"
        )
        ->where(
          "{$relatedModel->getTable()}.{$typeColumn}",
          $model->getMorphClass() // Получаем полное имя класса модели
        )
        ->limit(1);

      $query->orderBy($subQuery, $direction);
    }

    // Для BelongsTo
    elseif ($relation instanceof BelongsTo) {
      $foreignKey = $relation->getForeignKeyName();
      $ownerKey = $relation->getOwnerKeyName();

      $subQuery = $relatedModel->select($relationColumn)
        ->whereColumn(
          "{$model->getTable()}.{$foreignKey}",
          "{$relatedModel->getTable()}.{$ownerKey}"
        )
        ->limit(1);

      $query->orderBy($subQuery, $direction);
    }

    // Для HasOne/HasMany
    elseif ($relation instanceof HasOne || $relation instanceof HasMany) {
      $foreignKey = $relation->getForeignKeyName();
      $localKey = $relation->getLocalKeyName();

      $subQuery = $relatedModel->select($relationColumn)
        ->whereColumn(
          "{$relatedModel->getTable()}.{$foreignKey}",
          "{$model->getTable()}.{$localKey}"
        )
        ->latest() // или ->oldest() в зависимости от логики
        ->limit(1);

      $query->orderBy($subQuery, $direction);
    }

    // Для BelongsToMany
    elseif ($relation instanceof BelongsToMany) {
      $pivotTable = $relation->getTable();
      $relatedKey = $relation->getRelatedKeyName();

      $subQuery = $relatedModel->select($relationColumn)
        ->join($pivotTable, "{$pivotTable}.{$relatedKey}", "=", "{$relatedModel->getTable()}.id")
        ->whereColumn("{$pivotTable}.{$relation->getForeignPivotKeyName()}", "{$model->getTable()}.id")
        ->orderBy($relationColumn, $direction)
        ->limit(1);

      $query->orderBy($subQuery, $direction);
    }
  }

  /**
   * Применение сортировки
   */
  protected static function applySorting(Builder $query, array $params): void
  {
    if ($sortParams = Arr::get($params, 'sort_by')) {
      foreach ($sortParams as $sort) {
        $column = Arr::get($sort, 'key');
        $direction = Arr::get($sort, 'order', 'asc');

        if (!$column || !static::isSortable($column)) continue;

        if (str_contains($column, '.')) {
          self::applyRelationSort($query, $column, $direction);
        } else {
          $query->orderBy($column, $direction);
        }
      }
    }
  }


  /**
   * Проверка сортируемых полей
   */
  protected static function isSortable(string $column): bool
  {
    $sortable = property_exists(static::class, 'sortable')
      ? (new static)->sortable
      : ['id', 'created_at', 'updated_at'];

    return in_array($column, $sortable);
  }

  /**
   * Валидация параметров
   */
  protected static function validateParams(array $params): array
  {
    return Validator::make($params, [
      'search' => 'sometimes|string|max:255',
      'sort_by' => 'array',
      'sort_by.*.key' => 'required|string',
      'sort_by.*.order' => 'in:asc,desc',
      'items_per_page' => 'integer|min:1|max:100',
      'page' => 'integer|min:1',
      'limit' => 'integer|min:1|max:1000'
    ])->validate();
  }

  /**
   * Пагинируемый список
   */
  public static function getPaginateList(array $params = [], array $with = []): LengthAwarePaginator
  {
    try {
      $params = self::validateParams($params);
      $perPage = Arr::get($params, 'items_per_page', 15);
      $page = Arr::get($params, 'page', 1);

      return self::buildBaseQuery($params, $with)
        ->paginate($perPage, ['*'], 'page', $page);
    } catch (QueryException $e) {
      Log::error('Paginate error: ' . $e->getMessage());
      throw new \RuntimeException('Data retrieval error' . $e->getMessage());
    }
  }

  /**
   * Полный список с ограничением
   */
  public static function getList(array $params = [], array $with = [])
  {
    try {
      $params = self::validateParams($params);
      $limit = Arr::get($params, 'limit', 100);

      return self::buildBaseQuery($params, $with)
        ->limit($limit)
        ->get();
    } catch (QueryException $e) {
      Log::error('List error: ' . $e->getMessage());
      throw new \RuntimeException('Data retrieval error');
    }
  }
}
