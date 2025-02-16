<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        
        return $request->has(['items_per_page', 'page'])
            ? $this->model()::getPaginateList($request->all(), $with)
            : $this->model()::getList($request->all(), $with);
    }

    /**
     * Создание новой сущности
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $entity = $this->model()::createEntity($request->all());
            return $entity->load($request->input('with', []));
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

        return $this->model()::showEntity(
            $id, 
            $request->input('with', [])
        ) ?? throw new ModelNotFoundException();
    }

    /**
     * Обновление сущности
     */
    public function update(Request $request, string $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $entity = $this->model()::findOrFail($id);
            $entity->updateEntity($request->all());
            return $entity->load($request->input('with', []));
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
            'ids.*' => 'integer'
        ]);

        return DB::transaction(function () use ($validated) {
            $this->model()::destroy($validated['ids']);
            return response()->noContent();
        });
    }
}