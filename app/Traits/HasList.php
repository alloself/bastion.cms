<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
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

            if ($useFullText) {
                $q->whereFullText($fields, $search);
            } else {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            }
        });
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

                if ($column && static::isSortable($column)) {
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
