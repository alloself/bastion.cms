<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

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
     * Генерирует сайтмап при изменениях в модели Page
     */
    protected function triggerSitemapGeneration(): void
    {
        // Проверяем, является ли текущая модель страницей (Page)
        if ($this instanceof \App\Models\Page) {
            try {
                // Запускаем генерацию сайтмапа синхронно
                $exitCode = Artisan::call('sitemap:generate');
                
                if ($exitCode === 0) {
                    Log::info('Успешно сгенерирован сайтмап после изменения страницы', [
                        'page_id' => $this->id,
                        'action' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown'
                    ]);
                } else {
                    Log::warning('Команда генерации сайтмапа завершилась с ошибкой', [
                        'page_id' => $this->id,
                        'exit_code' => $exitCode
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Ошибка при запуске генерации сайтмапа', [
                    'page_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Синхронизирует отношения, вызывая соответствующий метод sync{Relation}.
     * Пустой массив тоже передаётся в метод синхронизации.
     * Если отношение отсутствует в данных, передается пустой массив для очистки связей.
     * Исключение: для 'link' метод не вызывается, если данные не переданы.
     *
     * @param array $relations
     */
    protected function syncRelations(array $relations): void
    {
        $linkChanged = false;
        
        // Проходим по всем разрешенным отношениям
        foreach ($this->syncableRelations as $relation) {
            $method = 'sync'.Str::studly($relation);
            
            // Проверяем, что метод существует
            if (method_exists($this, $method)) {
                // Специальная обработка для link - не вызываем метод, если данные не переданы
                if ($relation === 'link') {
                    // Вызываем syncLink только если ключ 'link' присутствует в данных
                    if (array_key_exists('link', $relations)) {
                        $data = $relations['link'];
                        // Вызываем метод синхронизации только если данные являются массивом
                        if (is_array($data)) {
                            $this->{$method}($data);
                            $linkChanged = true;
                        }
                    }
                } else {
                    // Для остальных отношений получаем данные или пустой массив, если ключ отсутствует
                    $data = $relations[$relation] ?? [];
                    
                    // Вызываем метод синхронизации только если данные являются массивом
                    if (is_array($data)) {
                        $this->{$method}($data);
                    }
                }
            }
        }

        // Генерируем сайтмап если изменилась ссылка у страницы
        if ($linkChanged) {
            $this->triggerSitemapGeneration();
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

        // Генерируем сайтмап для страниц
        $entity->triggerSitemapGeneration();

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

        if (in_array('contentBlocks', $with, true) && method_exists($entity, 'getContentBlocksTree')) {
            $entity->getContentBlocksTree();
        }
        if (in_array('dataCollections', $with, true) && method_exists($entity, 'getDataCollectionsTree')) {
            $entity->getDataCollectionsTree();
        }

        // Специальная обработка для ContentBlock - строим дерево children из descendants
        if (in_array('children', $with, true) && $entity instanceof \App\Models\ContentBlock) {
            if ($entity->relationLoaded('descendants') && $entity->descendants->isNotEmpty()) {
                $entity->setRelation('children', $entity->descendants->toTree($entity->id));
                $entity->unsetRelation('descendants');
            } else {
                $entity->setRelation('children', collect());
            }
        }

        // Специальная обработка для dataEntities - загружаем link для pivot
        if (in_array('dataEntities', $with, true) && method_exists($entity, 'loadDataEntityLinks')) {
            $entity->loadDataEntityLinks();
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

        // Генерируем сайтмап для страниц
        $this->triggerSitemapGeneration();

        if (!empty($with)) {
            $this->load(static::prepareRelations($with));
        }

        if (in_array('children', $with, true) && method_exists($this, 'getChildrenTree')) {
            $this->getChildrenTree();
        }

        if (in_array('contentBlocks', $with, true) && method_exists($this, 'getContentBlocksTree')) {
            $this->getContentBlocksTree();
        }
        if (in_array('dataCollections', $with, true) && method_exists($this, 'getDataCollectionsTree')) {
            $this->getDataCollectionsTree();
        }

        // Специальная обработка для ContentBlock - строим дерево children из descendants
        if (in_array('children', $with, true) && $this instanceof \App\Models\ContentBlock) {
            if ($this->relationLoaded('descendants') && $this->descendants->isNotEmpty()) {
                $this->setRelation('children', $this->descendants->toTree($this->id));
                $this->unsetRelation('descendants');
            } else {
                $this->setRelation('children', collect());
            }
        }

        // Специальная обработка для dataEntities - загружаем link для pivot
        if (in_array('dataEntities', $with, true) && method_exists($this, 'loadDataEntityLinks')) {
            $this->loadDataEntityLinks();
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
        // Генерируем сайтмап перед удалением для страниц
        $this->triggerSitemapGeneration();

        if (method_exists($this, 'children') && $this->children()->count()) {
            $this->children()->delete();
        }
        return $this->delete();
    }
}
