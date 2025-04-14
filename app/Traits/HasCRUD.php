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
     * Получение списка сущностей
     */
    public function index(Request $request)
    {
        $request->validate([
            'with' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $with = $request->input('with', []);

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

            $resourceClass = $this->resource();

            return new $resourceClass($entity->load($request->input('with', [])));
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

        $entity = $this->model()::showEntity(
            $id,
            $request->input('with', [])
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
            $entity->updateEntity($request->all(), $request->input('with', []));

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
