<?php

namespace App\Services;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;

class TemplateRenderer
{
    /**
     * Рендерит сущность используя её шаблон
     *
     * @param mixed $entity Сущность с шаблоном
     * @param array $data Дополнительные данные для передачи в шаблон
     * @return string|null Результат рендеринга
     */
    public function renderEntity($entity, array $data = []): ?string
    {
        if (!$entity->template || !isset($entity->template->value)) {
            return null;
        }
        
        // Подготавливаем relations, добавляя root если он не передан
        $relations = $data['relations'] ?? ['root' => $entity];
        if (!isset($relations['root'])) {
            $relations['root'] = $entity;
        }
        
        return Blade::render($entity->template->value, array_merge([
            'entity' => $entity,
            'meta' => $this->getMetaTags($entity),
            'images' => $this->prepareMediaUrls($entity),
            'relations' => $relations,
        ], $data));
    }
    
    /**
     * Извлекает meta-теги: title, description, keywords.
     */
    public function getMetaTags($model): array
    {
        return [
            'title'       => $model->meta_title ?? config('app.name'),
            'description' => $model->meta_description ?? '',
            'keywords'    => $model->meta_keywords ?? '',
        ];
    }
    
    /**
     * Подготавливает URL медиа-файлов (images и т.п.).
     */
    public function prepareMediaUrls($model): array
    {
        if (! method_exists($model, 'images') || ! $model->relationLoaded('images') || ! $model->images) {
            return [];
        }

        return $model->images->map(fn($img) => Storage::url($img->path))->toArray();
    }
} 