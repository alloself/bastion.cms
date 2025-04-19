<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasCRUDMethods
{
    /**
     * Список отношений, которые можно синхронизировать через syncRelations().
     * Ключи должны быть в snake_case.
     * sync{Relation} метод будет вызван для каждого совпадающего отношения, включая пустые массивы.
     * Пример: 'data_collections' -> syncDataCollections().
     *
     * @var string[]
     */
    protected array $syncableRelations = [
        'data_entities',
        'data_collections',
        'link',
        'attributes',
        'content_blocks',
        'images',
        'files',
    ];

    /**
     * Преобразует snake_case в camelCase
     */
    protected function snakeToCamel(string $input): string
    {
        return Str::camel($input);
    }

    /**
     * Синхронизирует отношения, вызывая соответствующий метод sync{Relation}.
     * Пустой массив тоже передаётся в метод синхронизации.
     *
     * @param array $relations
     */
    protected function syncRelations(array $relations): void
    {
        foreach ($relations as $relation => $data) {
            // проверяем, что отношение разрешено к синхронизации и метод существует
            if (!in_array($relation, $this->syncableRelations, true)) {
                continue;
            }

            $method = 'sync'.Str::studly($relation);
            if (method_exists($this, $method) && is_array($data)) {
                $this->{$method}($data);
            }
        }
    }

    /**
     * Создаёт сущность и синхронизирует отношения
     */
    public static function createEntity(array $data): self
    {
        $instance = new static;
        $relations = Arr::only($data, $instance->syncableRelations);
        $entity = static::query()->create(Arr::except($data, array_keys($relations)));
        $entity->syncRelations($relations);

        return $entity;
    }

    /**
     * Загружает сущность вместе с отношениями
     */
    public static function showEntity($id, array $with = []): self
    {
        $entity = static::with(static::prepareRelations($with))->findOrFail($id);
        if (in_array('children', $with, true) && method_exists($entity, 'getChildrenTree')) {
            $entity->getChildrenTree();
        }

        Log::alert($with);

        if (in_array('contentBlocks', $with, true) && method_exists($entity, 'getContentBlocksTree')) {
            $entity->getContentBlocksTree();
        }
        Log::alert($entity);
        if (in_array('dataCollections', $with, true) && method_exists($entity, 'getDataCollectionsTree')) {
            $entity->getDataCollectionsTree();
        }

        return $entity;
    }

    /**
     * Обновляет сущность и синхронизирует отношения
     */
    public function updateEntity(array $data, array $with = []): self
    {
        $relations = Arr::only($data, $this->syncableRelations);
        $this->update(Arr::except($data, array_keys($relations)));
        $this->syncRelations($relations);

        if (!empty($with)) {
            $this->load(static::prepareRelations($with));
        }

        if (in_array('data_collections', $with, true) && method_exists($this, 'getDataCollectionsTree')) {
            $this->getDataCollectionsTree();
        }
        if (in_array('content_blocks', $with, true) && method_exists($this, 'getContentBlocksTree')) {
            $this->getContentBlocksTree();
        }

        return $this;
    }

    /**
     * Готовит массив отношений для eager-load
     */
    protected static function prepareRelations(array $relations): array
    {
        $prepared = [];
        foreach ($relations as $relation) {
            if ($relation === 'children') {
                $prepared[] = 'descendants';
            } elseif (strpos($relation, 'children.') === 0) {
                $prepared[] = 'descendants.'.substr($relation, 9);
            } else {
                $prepared[] = $relation;
            }
        }
        return $prepared;
    }

    /**
     * Удаляет сущность и связанных потомков
     */
    public function deleteEntity(): bool
    {
        if (method_exists($this, 'children') && $this->children()->count()) {
            $this->children()->delete();
        }
        return $this->delete();
    }
}
