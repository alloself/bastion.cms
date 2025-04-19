<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\DataEntity;
use App\Models\Page;
use App\Models\ContentBlock;
use App\Models\DataCollection;
use App\Models\Pivot\DataEntityable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class SiteController extends Controller
{
    /**
     * Время кеширования страниц (в секундах)
     */
    protected $cacheTime = 3600; // 1 час

    /**
     * Метод для админки.
     */
    public function admin()
    {
        return view('admin');
    }

    /**
     * Основной метод рендера по slug.
     *
     * @param  Request  $request
     * @param  string   $url    Значение из {url?} маршрута
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request, string $url = '')
    {
        $slug = '/'.trim($url, '/');

        try {
            // Загружаем базовую информацию о ссылке с минимальным набором отношений
            // Оптимизация: используем select() для выборки только нужных полей
            $baseLink = Link::select('id', 'url', 'linkable_id', 'linkable_type')
                ->where('url', $slug)
                ->first();

            if (!$baseLink) {
                return $this->abort404();
            }
            
            // Определяем тип модели
            $linkableType = $baseLink->linkable_type;
            $query = Link::where('id', $baseLink->id);
            
            // Условная загрузка отношений в зависимости от типа модели
            // Оптимизация: ограничиваем глубину загрузки и выбираем только нужные поля
            if ($linkableType === Page::class) {
                $query->with([
                    'linkable:id,template_id,meta,index,parent_id', 
                    'linkable.template:id,value',
                    'linkable.children:id,parent_id,template_id,meta',
                    'linkable.children.template:id,value',
                    // Ограничиваем глубину загрузки вложенных отношений
                ]);
            } elseif ($linkableType === DataCollection::class) {
                $query->with([
                    'linkable:id,template_id,name,meta,parent_id',
                    'linkable.template:id,value',
                    // Не загружаем descendants и dataEntities здесь, загрузим их при необходимости в renderDataCollection
                ]);
            } elseif ($linkableType === ContentBlock::class) {
                $query->with([
                    'linkable:id,template_id,meta,parent_id',
                    'linkable.template:id,value',
                    // Не загружаем атрибуты и изображения здесь, загрузим их при необходимости в renderContentBlock
                ]);
            } elseif ($linkableType === DataEntity::class || $linkableType === DataEntityable::class) {
                $query->with([
                    'linkable:id,name,meta,template_id',
                    'linkable.template:id,value',
                    // Не загружаем атрибуты и изображения здесь, загрузим их при необходимости в renderDataEntity
                ]);
            } else {
                $query->with('linkable.template:id,value');
            }
            
            // Выполняем запрос с нужными отношениями
            $link = $query->first();
            
            if (!$link->linkable) {
                return $this->abort404();
            }

            $model = $link->linkable;

            $html = $this->renderContent($model, $link, $request);
        }
        catch (\Throwable $e) {
            dd($e);
            Log::error('Rendering error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->abort404();
        }

        return response($html, 200, [
            'Content-Type'   => 'text/html',
            'X-Robots-Tag'   => 'index,follow'
        ]);
    }

    /**
     * Выбирает и вызывает подходящий метод рендеринга в зависимости от типа модели.
     */
    protected function renderContent($model, Link $link, Request $request): string
    {
        if ($model instanceof Page) {
            return $this->renderPage($model, $request);
        } else if ($model instanceof DataCollection) {
            return $this->renderDataCollection($model, $link, $request);
        } else if ($model instanceof ContentBlock) {
            return $this->renderContentBlock($model, $link, $request);
        } else if ($model instanceof DataEntity) {
            return $this->renderDataEntity($model, $link, $request);
        } else if ($model instanceof DataEntityable) {
            return $this->renderDataEntityPivot($model, $link, $request);
        } else {
            return $this->abort404();
        }
    }

    /**
     * Рекурсивный рендер страницы (Page) с дочерними элементами и data collections.
     */
    protected function renderPage(Page $page, Request $request): string
    {
        if (! $page->template || ! isset($page->template->value)) {
            return $this->abort404();
        }

        // Загрузка необходимых связей, если они еще не загружены
        if (!$page->relationLoaded('contentBlocks')) {
            $page->load([
                'contentBlocks.attributes',
                'contentBlocks.descendants.attributes',
                'contentBlocks.files',
                'contentBlocks.descendants.files',
                'contentBlocks.images',
                'contentBlocks.template',
                'contentBlocks.descendants.template',
                'contentBlocks.descendants.images',
                'contentBlocks.dataCollections.descendants',
                'contentBlocks.descendants.dataCollections',
                'contentBlocks.dataCollections.dataEntities.attributes',
                'contentBlocks.dataCollections.dataEntities.images',
                'contentBlocks.dataCollections.dataEntities.link',
            ]);
        }

        // Подготовка блоков для дерева, если они есть
        if ($page->contentBlocks && $page->contentBlocks->isNotEmpty()) {
            $page->contentBlocks->map(function ($item) {
                if ($item && $item->relationLoaded('descendants')) {
                    $item->setRelation('children', $item->descendants->toTree($item->id));
                }
            });
        }

        // Получаем текущего пользователя
        $user = $request->user();
        if ($user) {
            unset($user->password);
            unset($user->remember_token);
        }

        // Находим header и footer среди contentBlocks
        $header = getItemByPivotKey($page->contentBlocks ?? collect(), 'header');
        $footer = getItemByPivotKey($page->contentBlocks ?? collect(), 'footer');

        // Рендер блоков контента
        $contentBlocks = $this->renderTree($page->contentBlocks ?? collect(), [
            'page' => $page, 
            'user' => $user,
            'header' => $header,
            'footer' => $footer
        ], 0, $request);

        // Рендер дочерних страниц
        $childrenHtml = '';
        if (isset($page->children) && $page->children) {
            foreach ($page->children as $child) {
                $childrenHtml .= $this->renderPage($child, $request);
            }
        }

        // Обработка data collections
        if (isset($page->dataCollections) && $page->dataCollections) {
            foreach ($page->dataCollections as $collection) {
                if (!isset($collection->pivot) || !isset($collection->pivot->paginate) || !$collection->pivot->paginate) {
                    continue;
                }

                $keyPrefix = isset($collection->pivot->key) ? $collection->pivot->key : 'default';
                $pageNum = $request->input("{$keyPrefix}_page", 1);
                $perPage = $request->input("{$keyPrefix}_per_page", 15);
                $sortBy  = $request->input("{$keyPrefix}_sort_by");
                $order   = $request->input("{$keyPrefix}_order", 'asc');

                $ids = [$collection->id];
                if ($collection->relationLoaded('descendants') && $collection->descendants) {
                    $ids = array_merge($ids, $collection->descendants->pluck('id')->toArray());
                }

                $query = DataEntity::whereIn('data_collection_id', $ids)
                    ->with(['attributes', 'images']);

                if ($sortBy) {
                    $query->orderBy($sortBy, $order);
                }

                $collection->setRelation(
                    'dataEntities',
                    $query->paginate($perPage, ['*'], "{$keyPrefix}_page", $pageNum)
                );
            }
        }

        // Подготавливаем данные для передачи в шаблон
        $viewData = [
            'page'          => $page,
            'children'      => $childrenHtml,
            'contentBlocks' => $contentBlocks,
            'user'          => $user,
            'meta'          => $this->getMetaTags($page),
            'images'        => $this->prepareMediaUrls($page),
            'header'        => $header,
            'footer'        => $footer
        ];

        return Blade::render($page->template->value, $viewData);
    }

    /**
     * Рекурсивный рендеринг дерева блоков.
     */
    protected function renderTree($contentBlocks, $globalVariables = [], int $depth = 0, Request $request)
    {
        $tree = [];
        $sortKey = $depth == 0 ? 'pivot.order' : 'order';

        foreach ($contentBlocks->sortByDesc($sortKey) as $key => $block) {
            // Обработка коллекций данных
            if ($block && isset($block->dataCollections) && $block->dataCollections) {
                foreach ($block->dataCollections as $dataCollection) {
                    if (!isset($dataCollection->pivot) || !isset($dataCollection->pivot->paginate) || !$dataCollection->pivot->paginate) {
                        continue;
                    }

                    $keyPage = $dataCollection->pivot->key . '_page';
                    $keyPerPage = $dataCollection->pivot->key . '_per_page';
                    $keySortBy = $dataCollection->pivot->key . '_sort_by';
                    $keyOrder = $dataCollection->pivot->key . '_order';

                    $page = $request->input($keyPage, 1);
                    $perPage = $request->input($keyPerPage, 15);
                    
                    $collectionsIds = [$dataCollection->id];
                    if ($dataCollection->relationLoaded('descendants')) {
                        $collectionsIds = array_merge($collectionsIds, 
                            $dataCollection->descendants->pluck('id')->toArray());
                    }

                    $entitiesQuery = DataEntity::whereIn('data_collection_id', $collectionsIds)
                        ->with(['attributes', 'images']);

                    $sortBy = $request->input($keySortBy);
                    $order = $request->input($keyOrder, 'asc');
                    
                    if ($sortBy) {
                        $entitiesQuery->orderBy($sortBy, $order);
                    }

                    $dataCollection->setRelation(
                        'dataEntities',
                        $entitiesQuery->paginate($perPage, ['*'], $keyPage, $page)
                    );

                    if ($dataCollection->relationLoaded('descendants')) {
                        $dataCollection->descendants->loadCount('dataEntities');
                        $dataCollection->setRelation('children', $dataCollection->descendants->toTree());
                    }
                }
            }

            // Рендеринг дочерних блоков, если они есть
            if ($block && isset($block->children) && count($block->children ?? [])) {
                $block->renderedChildren = $this->renderTree(
                    $block->children, 
                    $globalVariables, 
                    $depth + 1, 
                    $request
                );
            }

            // Добавляем ID блока в шаблон
            if ($block && isset($block->template) && $block->template && isset($block->template->value)) {
                $template = $block->template->value;
                $template = preg_replace('/^<\w+/im', "$0 id='block-$block->id'", trim($template));
                
                $tree[] = Blade::render(
                    $template,
                    [
                        'contentBlock' => $block,
                        'loop' => ['index' => $key, 'total' => count($contentBlocks->toArray())],
                        ...$globalVariables
                    ]
                );
            }
        }

        return $tree;
    }

    /**
     * Рендер для DataCollection.
     */
    protected function renderDataCollection(DataCollection $dataCollection, Link $link, Request $request): string
    {
        if (!$dataCollection->template || !isset($dataCollection->template->value)) {
            return $this->abort404();
        }

        // Загрузка необходимых отношений, если они еще не загружены
        if (!$dataCollection->relationLoaded('dataEntities')) {
            $dataCollection->load([
                'dataEntities.attributes',
                'dataEntities.images',
                'dataEntities.template',
                'descendants',
                'attributes',
                'images',
            ]);
        }

        // Если у DataCollection есть потомки, включаем их в запрос
        $ids = [$dataCollection->id];
        if ($dataCollection->relationLoaded('descendants') && $dataCollection->descendants) {
            $ids = array_merge($ids, $dataCollection->descendants->pluck('id')->toArray());
        }

        // Получаем сущности для данной коллекции и её потомков
        $query = DataEntity::whereIn('data_collection_id', $ids)
            ->with(['attributes', 'images', 'template']);

        // Пагинация, если нужна
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $entities = $query->paginate($perPage, ['*'], 'page', $page);

        // Рендерим шаблон
        return Blade::render($dataCollection->template->value, [
            'dataCollection' => $dataCollection,
            'dataEntities' => $entities,
            'meta' => $this->getMetaTags($dataCollection),
            'images' => $this->prepareMediaUrls($dataCollection),
        ]);
    }

    /**
     * Рендер для ContentBlock.
     */
    protected function renderContentBlock(ContentBlock $contentBlock, Link $link, Request $request): string
    {
        if (!$contentBlock->template || !isset($contentBlock->template->value)) {
            return $this->abort404();
        }

        // Загрузка необходимых связей, если они еще не загружены
        if (!$contentBlock->relationLoaded('attributes') || !$contentBlock->relationLoaded('images')) {
            $contentBlock->load([
                'attributes',
                'images',
                'descendants.attributes',
                'descendants.images',
                'descendants.template',
                'dataCollections.dataEntities.attributes',
                'dataCollections.dataEntities.images',
                'dataCollections.dataEntities.template',
            ]);
        }

        // Подготовка блоков для дерева, если есть потомки
        if ($contentBlock->relationLoaded('descendants') && $contentBlock->descendants->isNotEmpty()) {
            $contentBlock->setRelation('children', $contentBlock->descendants->toTree($contentBlock->id));
        }

        // Обработка data collections, если есть
        if ($contentBlock->relationLoaded('dataCollections') && $contentBlock->dataCollections->isNotEmpty()) {
            foreach ($contentBlock->dataCollections as $collection) {
                if (!isset($collection->pivot) || !isset($collection->pivot->paginate) || !$collection->pivot->paginate) {
                    continue;
                }

                $keyPrefix = isset($collection->pivot->key) ? $collection->pivot->key : 'default';
                $pageNum = $request->input("{$keyPrefix}_page", 1);
                $perPage = $request->input("{$keyPrefix}_per_page", 15);
                $sortBy  = $request->input("{$keyPrefix}_sort_by");
                $order   = $request->input("{$keyPrefix}_order", 'asc');

                $ids = [$collection->id];
                if ($collection->relationLoaded('descendants') && $collection->descendants) {
                    $ids = array_merge($ids, $collection->descendants->pluck('id')->toArray());
                }

                $query = DataEntity::whereIn('data_collection_id', $ids)
                    ->with(['attributes', 'images']);

                if ($sortBy) {
                    $query->orderBy($sortBy, $order);
                }

                $collection->setRelation(
                    'dataEntities',
                    $query->paginate($perPage, ['*'], "{$keyPrefix}_page", $pageNum)
                );
            }
        }

        return Blade::render($contentBlock->template->value, [
            'contentBlock' => $contentBlock,
            'meta' => $this->getMetaTags($contentBlock),
            'images' => $this->prepareMediaUrls($contentBlock),
        ]);
    }

    /**
     * Рендер для DataEntity.
     */
    protected function renderDataEntity(DataEntity $dataEntity, Link $link, Request $request): string
    {
        if (!$dataEntity->template || !isset($dataEntity->template->value)) {
            return $this->abort404();
        }

        // Загрузка необходимых связей, если они еще не загружены
        if (!$dataEntity->relationLoaded('attributes') || !$dataEntity->relationLoaded('images')) {
            $dataEntity->load([
                'attributes',
                'images',
                'variants.attributes',
                'variants.images',
                'variants.template',
            ]);
        }

        return Blade::render($dataEntity->template->value, [
            'dataEntity' => $dataEntity,
            'meta' => $this->getMetaTags($dataEntity),
            'images' => $this->prepareMediaUrls($dataEntity),
        ]);
    }

    /**
     * Рендер для DataEntityable (pivot).
     */
    protected function renderDataEntityPivot(DataEntityable $dataEntityable, Link $link, Request $request): string
    {
        // Получаем саму DataEntity со всеми необходимыми связями
        $dataEntity = DataEntity::with([
                'template', 
                'attributes', 
                'images',
                'variants.attributes',
                'variants.images',
                'variants.template'
            ])
            ->find($dataEntityable->data_entity_id);

        if (!$dataEntity || !$dataEntity->template || !isset($dataEntity->template->value)) {
            return $this->abort404();
        }

        // Загружаем связи для pivot модели, если требуется
        if (!$dataEntityable->relationLoaded('dataEntityable')) {
            $dataEntityable->load('dataEntityable');
        }

        return Blade::render($dataEntity->template->value, [
            'dataEntity' => $dataEntity,
            'meta' => $this->getMetaTags($dataEntity),
            'images' => $this->prepareMediaUrls($dataEntity),
            'pivot' => $dataEntityable,
            'parent' => $dataEntityable->dataEntityable,
        ]);
    }

    /**
     * Обработка страницы 404 — либо CMS-страница, либо стандартная.
     */
    protected function abort404()
    {
        $link404 = Link::with('linkable.template')
            ->where('slug', '404')
            ->first();

        if ($link404?->linkable?->template?->value) {
            $html404 = Blade::render($link404->linkable->template->value, [
                'page'   => $link404->linkable,
                'meta'   => $this->getMetaTags($link404->linkable),
                'images' => $this->prepareMediaUrls($link404->linkable),
            ]);
            return response($html404, 404, ['Content-Type' => 'text/html']);
        }

        return response(View::make('errors.404')->render(), 404, ['Content-Type' => 'text/html']);
    }

    /**
     * Подготавливает URL медиа-файлов (images и т.п.).
     */
    protected function prepareMediaUrls($model): array
    {
        if (! method_exists($model, 'images') || ! $model->relationLoaded('images') || ! $model->images) {
            return [];
        }

        return $model->images->map(fn($img) => Storage::url($img->path))->toArray();
    }

    /**
     * Извлекает meta-теги: title, description, keywords.
     */
    protected function getMetaTags($model): array
    {
        return [
            'title'       => $model->meta_title ?? config('app.name'),
            'description' => $model->meta_description ?? '',
            'keywords'    => $model->meta_keywords ?? '',
        ];
    }
}
