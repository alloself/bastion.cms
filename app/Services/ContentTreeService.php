<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;

class ContentTreeService
{
    /**
     * Рекурсивно рендерит дерево блоков
     */
    public function renderTree(Collection $contentBlocks, array $globalVariables = [], int $depth = 0, Request $request): array
    {
        $tree = [];
        $sortKey = $depth == 0 ? 'pivot.order' : 'order';

        foreach ($contentBlocks->sortByDesc($sortKey) as $key => $block) {
            // Обрабатываем коллекции данных при наличии
            $this->processPaginationForDataCollections($block, $request);
            
            // Рендерим дочерние блоки, если они есть
            $renderedChildren = [];
            if ($block && isset($block->children) && $block->children->isNotEmpty()) {
                $renderedChildren = $this->renderTree(
                    $block->children,
                    $globalVariables,
                    $depth + 1,
                    $request
                );
            }

            // Рендерим текущий блок, если у него есть шаблон
            if ($block && isset($block->template) && $block->template && isset($block->template->value)) {
                $template = $block->template->value;
                $template = preg_replace('/^<\w+/im', "$0 id='block-$block->id'", trim($template));

                $renderedTemplate = Blade::render(
                    $template,
                    [
                        'contentBlock' => $block,
                        'loop' => ['index' => $key, 'total' => count($contentBlocks->toArray())],
                        'children' => $renderedChildren,
                        ...$globalVariables
                    ]
                );
                
                $block->render = $renderedTemplate;
                $tree[] = $renderedTemplate;
            }
        }

        return $tree;
    }
    
    /**
     * Подготавливает древовидную структуру для контент-блоков
     */
    public function prepareBlocksTree(Collection $blocks): Collection
    {
        return $blocks->map(function ($item) {
            if ($item && $item->relationLoaded('descendants')) {
                $item->setRelation('children', $item->descendants->toTree($item->id));
            }
            return $item;
        });
    }
    
    /**
     * Обрабатывает пагинацию для коллекций данных внутри блока
     */
    private function processPaginationForDataCollections($block, Request $request): void
    {
        if (!$block || !isset($block->dataCollections) || !$block->dataCollections) {
            return;
        }
        
        foreach ($block->dataCollections as $dataCollection) {
            if (!isset($dataCollection->pivot) || !isset($dataCollection->pivot->paginate) || !$dataCollection->pivot->paginate) {
                continue;
            }

            // Формируем ключ для пагинации
            // Используем ключ из pivot если есть, иначе фиксированный ключ
            $keyPrefix = isset($dataCollection->pivot->key) && !empty($dataCollection->pivot->key)
                ? $dataCollection->pivot->key
                : "";
                
            // Если контент-блок имеет свой ключ, добавляем его как префикс
            if (isset($block->pivot) && isset($block->pivot->key) && !empty($block->pivot->key)) {
                $keyPrefix = !empty($keyPrefix) ? "{$block->pivot->key}_{$keyPrefix}" : $block->pivot->key;
            }
            
            // Получаем сущности с пагинацией, используя сервис
            $dataEntityService = app(DataEntityService::class);
            
            // Передаем параметры в сервис напрямую, без клонирования запроса
            $dataEntities = $dataEntityService->paginateEntities(
                $dataCollection,
                $request,
                $keyPrefix
            );
            
            // Устанавливаем отношение между коллекцией и пагинированными сущностями
            $dataCollection->setRelation('dataEntities', $dataEntities);
            
            // Настраиваем ссылки и другие отношения
            if (method_exists($dataEntityService, 'transferLinksFromPivots')) {
                $dataEntityService->transferLinksFromPivots($dataEntities);
            }
            
            if (method_exists($dataEntityService, 'transferDataCollectionInfoFromPivots')) {
                $dataEntityService->transferDataCollectionInfoFromPivots($dataEntities);
            }

            // Обрабатываем дочерние коллекции
            if ($dataCollection->relationLoaded('descendants')) {
                $dataCollection->descendants->loadCount('dataEntities');
                $dataCollection->setRelation('children', $dataCollection->descendants->toTree());
            } 
        }
    }
} 