<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\DataEntity;
use App\Models\Page;
use App\Models\ContentBlock;
use App\Models\DataCollection;
use App\Models\Pivot\DataEntityable;
use App\Services\ContentTreeService;
use App\Services\DataEntityService;
use App\Services\RelationsLoader;
use App\Services\TemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    protected $relationsLoader;
    protected $templateRenderer;
    protected $contentTreeService;
    protected $dataEntityService;

    public function __construct(
        RelationsLoader $relationsLoader,
        TemplateRenderer $templateRenderer,
        ContentTreeService $contentTreeService,
        DataEntityService $dataEntityService
    ) {
        $this->relationsLoader = $relationsLoader;
        $this->templateRenderer = $templateRenderer;
        $this->contentTreeService = $contentTreeService;
        $this->dataEntityService = $dataEntityService;
    }

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
        $slug = '/' . trim($url, '/');

        try {
            
            $baseLink = Link::select('id', 'url', 'linkable_id', 'linkable_type')
                ->where('url', $slug)
                ->first();
         
            if (!$baseLink) {
                return $this->abort404();
            }

            $linkableType = $baseLink->linkable_type;
            $query = Link::with('linkable')->where('id', $baseLink->id);

            $this->loadLinkRelations($query, $linkableType);

            $link = $query->first();
            if (!$link?->linkable) {
                return $this->abort404();
            }

            $model = $link->linkable;

            $html = $this->renderContent($model, $link, $request);
        } catch (\Throwable $e) {
            Log::error('Rendering error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->abort404();
        }

        return response($html, 200, [
            'Content-Type'   => 'text/html',
            'X-Robots-Tag'   => 'index,follow'
        ]);
    }

    /**
     * Загружает нужные связи для запроса по ссылке
     */
    protected function loadLinkRelations($query, $linkableType): void
    {
        // Поддерживаем как константы класса, так и строковые значения
        $isPage = ($linkableType === Page::class || $linkableType === 'App\\Models\\Page');
        $isDataCollection = ($linkableType === DataCollection::class || $linkableType === 'App\\Models\\DataCollection');
        $isContentBlock = ($linkableType === ContentBlock::class || $linkableType === 'App\\Models\\ContentBlock');
        $isDataEntity = ($linkableType === DataEntity::class || $linkableType === 'App\\Models\\DataEntity');
        $isDataEntityable = ($linkableType === DataEntityable::class || $linkableType === 'App\\Models\\Pivot\\DataEntityable');

        if ($isPage) {
            $query->with([
                'linkable',
                'linkable.template',
                'linkable.children',
                'linkable.children.template',
            ]);
        } elseif ($isDataCollection) {
            $query->with([
                'linkable',
                'linkable.template',
            ]);
        } elseif ($isContentBlock) {
            $query->with([
                'linkable',
                'linkable.template',
            ]);
        } elseif ($isDataEntity) {
            $query->with([
                'linkable',
               'linkable.template',
            ]);
        } elseif ($isDataEntityable) {
            $query->with([
                'linkable',
                'linkable.dataEntity',
                'linkable.dataEntity.template',
                'linkable.dataEntity.attributes',
                'linkable.dataEntity.images',
                'linkable.dataEntityable',
                'linkable.dataEntityable.link',
            ]);
        } else {
            $query->with('linkable.template');
        }
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
        if (!$page->template || !isset($page->template->value)) {
            return $this->abort404();
        }

        // Загружаем все необходимые связи
        $this->relationsLoader->loadPageRelations($page);

        // Подготовка блоков для дерева
        if ($page->contentBlocks && $page->contentBlocks->isNotEmpty()) {
            $this->contentTreeService->prepareBlocksTree($page->contentBlocks);
        }

        // Получаем текущего пользователя
        $user = $request->user();

        // Находим header и footer среди contentBlocks
        $header = getItemByPivotKey($page->contentBlocks ?? collect(), 'header');
        $footer = getItemByPivotKey($page->contentBlocks ?? collect(), 'footer');

        // Рендер блоков контента
        $contentBlocks = $this->contentTreeService->renderTree(
            $page->contentBlocks ?? collect(), 
            [
                'page' => $page,
                'user' => $user,
                'header' => $header,
                'footer' => $footer
            ], 
            0, 
            $request
        );

        // Обеспечиваем, что $contentBlocks всегда определено
        if (empty($contentBlocks)) {
            $contentBlocks = [];
        }

        // Подготавливаем данные для передачи в шаблон
        $viewData = [
            'entity'        => $page,
            'contentBlocks' => $contentBlocks,
            'user'          => $user,
            'meta'          => $this->templateRenderer->getMetaTags($page),
            'images'        => $this->templateRenderer->prepareMediaUrls($page),
            'header'        => $header,
            'footer'        => $footer,
            'relations'     => ['root' => $page]
        ];

        return Blade::render($page->template->value, $viewData);
    }

    /**
     * Рендер для DataCollection.
     */
    protected function renderDataCollection(DataCollection $dataCollection, Link $link, Request $request): string
    {
        if (!$dataCollection->template || !isset($dataCollection->template->value)) {
            return $this->abort404();
        }

        // Загружаем связи
        $this->relationsLoader->loadDataCollectionRelations($dataCollection);

        // Подготавливаем структуру дерева для потомков
        $descendants = $dataCollection->descendants;
        if ($descendants->isNotEmpty()) {
            $dataCollection->setRelation('children', $descendants->toTree());
        } else {
            $dataCollection->setRelation('children', collect([]));
        }

        // Пагинируем сущности данных
        $dataEntities = $this->dataEntityService->paginateEntities(
            $dataCollection, 
            $request
        );
        
        // Устанавливаем отношение с пагинацией
        $dataCollection->setRelation('dataEntities', $dataEntities);

        // Перенос ссылок из dataEntityables.link в dataEntity.link
        $this->dataEntityService->transferLinksFromPivots($dataEntities);
        
        // Перенос информации о dataCollection из dataEntityables.dataEntityable в dataEntity.dataCollection
        $this->dataEntityService->transferDataCollectionInfoFromPivots($dataEntities);

        // Подготовка блоков контента для дерева
        if ($dataCollection->contentBlocks && $dataCollection->contentBlocks->isNotEmpty()) {
            $this->contentTreeService->prepareBlocksTree($dataCollection->contentBlocks);
        }

        // Получаем текущего пользователя
        $user = $request->user();
       
        // Находим header и footer среди contentBlocks
        $header = getItemByPivotKey($dataCollection->contentBlocks ?? collect(), 'header');
        $footer = getItemByPivotKey($dataCollection->contentBlocks ?? collect(), 'footer');
        
        // Рендер блоков контента
        $contentBlocks = $this->contentTreeService->renderTree(
            $dataCollection->contentBlocks ?? collect(), 
            [
                'entity' => $dataCollection,
                'user' => $user,
                'header' => $header,
                'footer' => $footer
            ], 
            0, 
            $request
        );

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

        // Рендерим шаблон с данными
        return $this->templateRenderer->renderEntity($dataCollection, [
            'contentBlocks' => $contentBlocks ?? [],
            'user' => $user,
            'header' => $header,
            'footer' => $footer,
            'relations' => [
                'root' => $dataCollection->root ?? $dataCollection
            ]
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

        // Загружаем связи
        $this->relationsLoader->loadContentBlockRelations($contentBlock);

        // Подготовка блоков для дерева, если есть потомки
        if ($contentBlock->relationLoaded('descendants') && $contentBlock->descendants->isNotEmpty()) {
            $contentBlock->setRelation('children', $contentBlock->descendants->toTree($contentBlock->id));
        } else {
            $contentBlock->setRelation('children', collect([]));
        }

        // Преобразуем descendants в children для каждой коллекции данных
        if ($contentBlock->relationLoaded('dataCollections') && $contentBlock->dataCollections->isNotEmpty()) {
            foreach ($contentBlock->dataCollections as $dataCollection) {
                if ($dataCollection->relationLoaded('descendants')) {
                    $dataCollection->setRelation('children', $dataCollection->descendants->toTree());
                }
                
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
            }
        }

        $user = $request->user();

        // Рендерим дочерние блоки
        $children = [];
        if ($contentBlock->children && $contentBlock->children->isNotEmpty()) {
            $children = $this->contentTreeService->renderTree(
                $contentBlock->children,
                ['contentBlock' => $contentBlock, 'user' => $user],
                0,
                $request
            );
        }

        return $this->templateRenderer->renderEntity($contentBlock, [
            'children' => $children,
            'user' => $user,
            'relations' => [
                'root' => $contentBlock
            ]
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

        return $this->templateRenderer->renderEntity($dataEntity, [
            'relations' => [
                'root' => $dataEntity
            ]
        ]);
    }

    /**
     * Рендер для DataEntityable (pivot).
     */
    protected function renderDataEntityPivot(DataEntityable $dataEntityable, Link $link, Request $request): string
    {
        // Получаем саму DataEntity со всеми необходимыми связями
        $dataEntity = $this->relationsLoader->loadDataEntityPivotRelations($dataEntityable);

        if (!$dataEntity || !$dataEntity->template || !isset($dataEntity->template->value)) {
            return $this->abort404();
        }

        // Загружаем связи для pivot модели, если требуется
        if (!$dataEntityable->relationLoaded('dataEntityable')) {
            $dataEntityable->load('dataEntityable.link');
        }

        // Если у DataEntityable есть link, переносим его в dataEntity.link
        if ($link) {
            $dataEntity->setRelation('link', $link);
        }
        
        // Если dataEntityable типа DataCollection, устанавливаем его как dataCollection для dataEntity
        if ($dataEntityable->data_entityable_type === DataCollection::class) {
            $dataCollection = $dataEntityable->dataEntityable;
            $dataEntity->setRelation('dataCollection', $dataCollection);
            
            // Если у коллекции данных есть link, устанавливаем его как dataCollectionLink
            if ($dataCollection && $dataCollection->relationLoaded('link') && $dataCollection->link) {
                $dataEntity->setRelation('dataCollectionLink', $dataCollection->link);
            }
        }

        // Подготовка блоков для дерева
        if ($dataEntity->contentBlocks && $dataEntity->contentBlocks->isNotEmpty()) {
            $this->contentTreeService->prepareBlocksTree($dataEntity->contentBlocks);
        }

        // Получаем текущего пользователя
        $user = $request->user();
        if ($user) {
            unset($user->password);
            unset($user->remember_token);
        }

        // Находим header и footer среди contentBlocks
        $header = getItemByPivotKey($dataEntity->contentBlocks ?? collect(), 'header');
        $footer = getItemByPivotKey($dataEntity->contentBlocks ?? collect(), 'footer');

        // Рендер блоков контента
        $contentBlocks = $this->contentTreeService->renderTree(
            $dataEntity->contentBlocks ?? collect(), 
            [
                'entity' => $dataEntity,
                'user' => $user,
                'header' => $header,
                'footer' => $footer,
                'pivot' => $dataEntityable,
                'parent' => $dataEntityable->dataEntityable
            ], 
            0, 
            $request
        );

        return $this->templateRenderer->renderEntity($dataEntity, [
            'pivot' => $dataEntityable,
            'parent' => $dataEntityable->dataEntityable,
            'contentBlocks' => $contentBlocks ?? [],
            'header' => $header,
            'footer' => $footer,
            'user' => $user,
            'relations' => [
                'root' => $dataEntity->relationLoaded('dataCollection') ? $dataEntity->dataCollection : $dataEntity
            ]
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
                'meta'   => $this->templateRenderer->getMetaTags($link404->linkable),
                'images' => $this->templateRenderer->prepareMediaUrls($link404->linkable),
                'contentBlocks' => [],
                'header' => null,
                'footer' => null,
                'user'   => request()->user()
            ]);
            return response($html404, 404, ['Content-Type' => 'text/html']);
        }

        return $this->show404();
    }

    /**
     * Публичный метод для отображения страницы 404.
     * 
     * @return \Illuminate\Http\Response
     */
    public function show404()
    {
        return response(View::make('errors.404')->render(), 404, ['Content-Type' => 'text/html']);
    }
}
