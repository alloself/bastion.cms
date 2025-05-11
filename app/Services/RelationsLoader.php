<?php

namespace App\Services;

use App\Models\Page;
use App\Models\DataEntity;
use App\Models\DataCollection;
use App\Models\ContentBlock;
use App\Models\Pivot\DataEntityable;

class RelationsLoader
{
    /**
     * Загружает отношения для страницы
     * Оптимизировано для уменьшения количества запросов к БД
     */
    public function loadPageRelations(Page $page): Page
    {
        // Группируем связанные отношения для уменьшения количества запросов
        return $page->load([
            // Базовые отношения контент-блоков
            'contentBlocks' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.template',
            'contentBlocks.link',
            'contentBlocks.attributes',
            'contentBlocks.images',
            'contentBlocks.files',
            
            // Дочерние блоки и их базовые отношения
            'contentBlocks.descendants' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.descendants.template',
            'contentBlocks.descendants.link',
            'contentBlocks.descendants.attributes',
            'contentBlocks.descendants.images',
            'contentBlocks.descendants.files',
            
            // Коллекции данных и их отношения
            'contentBlocks.dataCollections.descendants',
            'contentBlocks.dataCollections.images',
            'contentBlocks.descendants.dataCollections',
            
            // Сущности данных и их отношения
            'contentBlocks.dataEntities.attributes',
            'contentBlocks.dataEntities.images',
            'contentBlocks.dataEntities.template',
            'contentBlocks.dataEntities.dataEntityables.link',
            'contentBlocks.descendants.dataEntities.attributes',
            'contentBlocks.descendants.dataEntities.images',
            'contentBlocks.descendants.dataEntities.template',
            'contentBlocks.descendants.dataEntities.dataEntityables.link',
            
            // Сущности данных коллекций
            'contentBlocks.dataCollections.dataEntities' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.dataCollections.dataEntities.attributes',
            'contentBlocks.dataCollections.dataEntities.images',
        ]);
    }

    /**
     * Загружает отношения для коллекции данных
     * Оптимизировано для уменьшения количества запросов к БД
     */
    public function loadDataCollectionRelations(DataCollection $dataCollection): DataCollection
    {
        // Находим корневую сущность для коллекции
        $root = $dataCollection->isRoot() 
            ? $dataCollection 
            : DataCollection::whereIsRoot()->whereAncestorOf($dataCollection)->first();
            
        if ($root && $root->id !== $dataCollection->id) {
            // Загружаем основные отношения для root
            $root->load([
                'attributes',
                'images',
                'link',
                'template',
                'contentBlocks' => function($query) {
                    $query->orderBy('order');
                }
            ]);
            $dataCollection->setRelation('root', $root);
        } else {
            $dataCollection->setRelation('root', $dataCollection);
        }
            
        return $dataCollection->load([
            // Базовые отношения
            'attributes',
            'images',
            'descendants',
            
            // Контент-блоки и их базовые отношения
            'contentBlocks' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.template',
            'contentBlocks.link',
            'contentBlocks.attributes',
            'contentBlocks.images',
            'contentBlocks.files',
            
            // Дочерние блоки и их отношения
            'contentBlocks.descendants' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.descendants.template',
            'contentBlocks.descendants.link',
            'contentBlocks.descendants.attributes',
            'contentBlocks.descendants.images',
            
            // Коллекции данных и их отношения
            'contentBlocks.dataCollections.descendants',
            'contentBlocks.dataCollections.images',
            'contentBlocks.descendants.dataCollections',
            
            // Сущности данных коллекций
            'contentBlocks.dataCollections.dataEntities' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.dataCollections.dataEntities.attributes',
            'contentBlocks.dataCollections.dataEntities.images',
        ]);
    }

    /**
     * Загружает отношения для контент-блока
     * Оптимизировано для уменьшения количества запросов к БД
     */
    public function loadContentBlockRelations(ContentBlock $contentBlock): ContentBlock
    {
        return $contentBlock->load([
            // Базовые отношения
            'attributes',
            'images', 
            'link',
            
            // Дочерние элементы и их отношения
            'descendants' => function($query) {
                $query->orderBy('order');
            },
            'descendants.attributes',
            'descendants.images',
            'descendants.template',
            'descendants.link',
            
            // Коллекции данных и их отношения
            'dataCollections' => function($query) {
                $query->orderBy('order');
            },
            'dataCollections.descendants',
            'dataCollections.dataEntities' => function($query) {
                $query->orderBy('order');
            },
            'dataCollections.dataEntities.attributes',
            'dataCollections.dataEntities.images',
            'dataCollections.dataEntities.template',
        ]);
    }

    /**
     * Загружает отношения для сущности данных через пивот
     * Оптимизировано для уменьшения количества запросов к БД
     */
    public function loadDataEntityPivotRelations(DataEntityable $dataEntityable): DataEntity
    {
        return DataEntity::with([
            // Базовые отношения
            'template',
            'attributes',
            'images',
            
            // Контент-блоки и их отношения
            'contentBlocks' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.template',
            'contentBlocks.link',
            'contentBlocks.attributes',
            'contentBlocks.images',
            'contentBlocks.files',
            
            // Дочерние блоки и их отношения
            'contentBlocks.descendants' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.descendants.template',
            'contentBlocks.descendants.link',
            'contentBlocks.descendants.attributes',
            'contentBlocks.descendants.images',
            
            // Коллекции данных и их отношения
            'contentBlocks.dataCollections' => function($query) {
                $query->orderBy('order');
            },
            'contentBlocks.dataCollections.descendants',
            'contentBlocks.dataCollections.images',
            'contentBlocks.dataCollections.dataEntities',
            
            // Варианты и их отношения
            'variants' => function($query) {
                $query->orderBy('order');
            },
            'variants.attributes',
            'variants.images',
            'variants.template'
        ])
            ->find($dataEntityable->data_entity_id);
    }
} 