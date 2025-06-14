<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

trait HasCRUD
{
    /**
     * Абстрактный метод для получения класса модели
     */
    abstract public function model(): string;

    /**
     * Фильтрует запрашиваемые связи через allowedRelations с поддержкой вложенных связей
     */
    protected function filterRelations(array $requestedWith): array
    {
        $allowedRelations = $this->allowedRelations();
        $filteredWith = [];

        foreach ($requestedWith as $relation) {
            // Проверяем, есть ли связь в списке разрешенных
            if (in_array($relation, $allowedRelations)) {
                $filteredWith[] = $relation;
            }
        }

        return $filteredWith;
    }

    /**
     * Получение списка сущностей
     */
    public function index(Request $request)
    {
        $request->validate([
            'with' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        // Фильтруем связи через allowedRelations
        $requestedWith = $request->input('with', []);
        $with = $this->filterRelations($requestedWith);

        $data = $request->has(['items_per_page', 'page'])
            ? $this->model()::getPaginateList($request->all(), $with)
            : $this->model()::getList($request->all(), $with);

        $resourceClass = $this->resource();

        return $resourceClass::collection($data);
    }

    /**
     * Создание новой сущности
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $entity = $this->model()::createEntity($request->all());

            // Фильтруем связи через allowedRelations
            $requestedWith = $request->input('with', []);
            $filteredWith = $this->filterRelations($requestedWith);

            $resourceClass = $this->resource();

            return new $resourceClass($entity->load($filteredWith));
        });
    }

    /**
     * Просмотр конкретной сущности
     */
    public function show(string $id, Request $request)
    {
        $request->validate([
            'with' => 'sometimes|array'
        ]);

        // Фильтруем связи через allowedRelations
        $requestedWith = $request->input('with', []);
        $filteredWith = $this->filterRelations($requestedWith);

        $entity = $this->model()::showEntity(
            $id,
            $filteredWith
        ) ?? throw new ModelNotFoundException();
        
        $resourceClass = $this->resource();

        return new $resourceClass($entity);
    }

    /**
     * Обновление сущности
     */
    public function update(Request $request, string $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $entity = $this->model()::findOrFail($id);
            
            // Фильтруем связи через allowedRelations
            $requestedWith = $request->input('with', []);
            $filteredWith = $this->filterRelations($requestedWith);
            
            $entity->updateEntity($request->all(), $filteredWith);

            $resourceClass = $this->resource();

            return new $resourceClass($entity);
        });
    }

    /**
     * Удаление сущности
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $entity = $this->model()::findOrFail($id);
            $entity->deleteEntity();
            return response()->noContent();
        });
    }

    /**
     * Массовое удаление сущностей
     */
    public function deleteMany(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string'
        ]);

        return DB::transaction(function () use ($validated) {
            $entities = $this->model()::whereIn('id', $validated['ids'])->get();

            foreach ($entities as $entity) {
                $entity->deleteEntity();
            }
            return response()->noContent();
        });
    }
}
