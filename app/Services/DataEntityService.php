<?php

namespace App\Services;

use App\Models\DataEntity;
use App\Models\DataCollection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DataEntityService
{
    /**
     * Пагинирует сущности данных для коллекции
     * 
     * @param DataCollection $collection Коллекция данных
     * @param Request $request Запрос с параметрами пагинации и сортировки
     * @param string $keyPrefix Префикс для параметров запроса (если пустой, используются стандартные параметры)
     * @param array $filters Дополнительные фильтры для запроса
     * @return LengthAwarePaginator
     */
    public function paginateEntities(
        DataCollection $collection, 
        Request $request, 
        string $keyPrefix = '',
        array $filters = []
    ): LengthAwarePaginator
    {
        // Определяем, используем ли мы префикс
        $usePrefix = !empty($keyPrefix);
        
        // Получаем параметры пагинации из запроса без его изменения
        if ($usePrefix) {
            // Режим с префиксом
            $page = (int) $request->input("{$keyPrefix}_page", 1);
            $perPage = (int) $request->input("{$keyPrefix}_per_page", 15);
            $sortBy = $request->input("{$keyPrefix}_sort_by");
            $order = strtolower($request->input("{$keyPrefix}_order", 'asc'));
            $sortByAttribute = $request->input("{$keyPrefix}_sort_by_attribute");
            
            // Для пагинатора будем использовать имя страницы с префиксом
            $pageName = "{$keyPrefix}_page";
        } else {
            // Режим без префикса
            $page = (int) $request->input("page", 1);
            $perPage = (int) $request->input("per_page", 15);
            $sortBy = $request->input("sort_by");
            $order = strtolower($request->input("order", 'asc'));
            $sortByAttribute = $request->input("sort_by_attribute");
            
            // Стандартное имя страницы для пагинатора
            $pageName = "page";
        }
        

        
        // Проверка валидности параметров
        $page = max(1, $page);
        $perPage = max(1, min($perPage, 15)); // Уменьшаем максимум
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';
        
        // Получаем список ID коллекций (текущая + потомки)
        $collectionIds = $this->getCollectionWithDescendantIds($collection);
        

        
        // Создаем базовый запрос с использованием Eloquent
        $query = DataEntity::with([
            'attributes',
            'images',
            'template',
            'dataEntityables' => function ($q) use ($collectionIds) {
                $q->whereIn('data_entityable_id', $collectionIds)
                  ->where('data_entityable_type', DataCollection::class)
                  ->orderBy('order')
                  ->with([
                      'link',
                      'dataEntityable' => function($q) {
                          $q->with(['link', 'template']);
                      }
                  ]);
            }
        ])->whereHas('dataEntityables', function ($q) use ($collectionIds) {
            $q->whereIn('data_entityable_id', $collectionIds)
              ->where('data_entityable_type', DataCollection::class);
        });
        
        // Применяем дополнительные фильтры, если они переданы
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        
        // Применяем фильтры из URL
        if ($usePrefix) {
            $this->applyUrlFiltersWithPrefix($query, $request, $keyPrefix);
        } else {
            $this->applyUrlFilters($query, $request);
        }

        // Сортировка по атрибутам или обычным полям
        if ($sortByAttribute) {
            // Сортировка по атрибуту
            if ($sortByAttribute === 'price') {
                // Для цены используем подзапрос с числовой сортировкой
                $query->select('data_entities.*')
                    ->selectSub(function ($subquery) {
                        $subquery->select('attributeables.value')
                            ->from('attributeables')
                            ->join('attributes', 'attributeables.attribute_id', '=', 'attributes.id')
                            ->whereColumn('attributeables.attributeable_id', 'data_entities.id')
                            ->where('attributeables.attributeable_type', DataEntity::class)
                            ->where('attributes.key', 'price')
                            ->whereRaw('attributeables.value REGEXP "^[0-9]+(\\.[0-9]+)?$"')
                            ->limit(1);
                    }, 'price_value')
                    ->whereHas('attributes', function ($q) {
                        $q->where('key', 'price')
                          ->whereRaw('attributeables.value REGEXP "^[0-9]+(\\.[0-9]+)?$"');
                    })
                    ->orderByRaw('CAST(price_value AS DECIMAL(20,2)) ' . $order)
                    ->orderBy('id', 'asc');
            } else {
                // Сортировка по другим атрибутам
                $query->select('data_entities.*')
                    ->join('attributeables', function ($join) {
                        $join->on('data_entities.id', '=', 'attributeables.attributeable_id')
                             ->where('attributeables.attributeable_type', '=', DataEntity::class);
                    })
                    ->join('attributes', function ($join) use ($sortByAttribute) {
                        $join->on('attributeables.attribute_id', '=', 'attributes.id')
                             ->where('attributes.key', '=', $sortByAttribute);
                    })
                    ->addSelect('attributeables.value')
                    ->orderBy('attributeables.value', $order)
                    ->orderBy('data_entities.id', 'asc')
                    ->distinct();
            }
        } else if ($sortBy) {
            // Сортировка по полям модели
            if ($sortBy === 'pivot.order' || $sortBy === 'order') {
                // Сортировка по порядку в pivot
                $query->select('data_entities.*')
                    ->join('data_entityables', function ($join) use ($collectionIds) {
                        $join->on('data_entities.id', '=', 'data_entityables.data_entity_id')
                             ->whereIn('data_entityables.data_entityable_id', $collectionIds)
                             ->where('data_entityables.data_entityable_type', '=', DataCollection::class);
                    })
                    ->orderBy('data_entityables.order', $order)
                    ->orderBy('data_entities.id', 'asc')
                    ->distinct();
            } else {
                // Обычная сортировка по полю модели
                $query->orderBy($sortBy, $order)
                      ->orderBy('id', 'asc');
            }
        } else {
            // Сортировка по умолчанию
            $query->orderBy('order', 'asc')
                  ->orderBy('id', 'asc');
        }

        // Создаем пагинатор с учетом префикса
        $dataEntities = $query->paginate(
            $perPage, 
            ['*'], 
            $pageName, 
            $page
        );
        
        // Добавляем параметры запроса к пагинатору
        $paginationParams = [];
        foreach ($request->all() as $param => $value) {
            if ($usePrefix) {
                if (strpos($param, "{$keyPrefix}_") === 0) {
                    $paginationParams[$param] = $value;
                }
            } else {
                if (strpos($param, '_') === false || 
                    in_array($param, ['page', 'per_page', 'sort_by', 'order', 'sort_by_attribute']) || 
                    strpos($param, 'filter_') === 0) {
                    $paginationParams[$param] = $value;
                }
            }
        }
        
        if (!empty($paginationParams)) {
            $dataEntities->appends($paginationParams);
        }
        
        return $dataEntities;
    }
    
    /**
     * Перемещает ссылки из pivot-отношений к сущностям
     */
    public function transferLinksFromPivots($entities): void
    {
        foreach ($entities as $entity) {
            if ($entity->dataEntityables->isNotEmpty()) {
                // Получаем первый pivot с link
                $pivotWithLink = $entity->dataEntityables->first(function ($pivot) {
                    // Проверяем, загружена ли связь link у pivot
                    return $pivot->relationLoaded('link') && $pivot->link !== null;
                });

                // Если нашли pivot с link, присваиваем его сущности
                if ($pivotWithLink && $pivotWithLink->link) {
                    $entity->setRelation('link', $pivotWithLink->link);
                }
            }
        }
    }
    
    /**
     * Связывает информацию о dataCollection с каждой сущностью
     * для удобного доступа в шаблонах
     */
    public function transferDataCollectionInfoFromPivots($entities): void
    {
        foreach ($entities as $entity) {
            if ($entity->dataEntityables->isNotEmpty()) {
                // Для каждой сущности данных
                foreach ($entity->dataEntityables as $pivot) {
                    // Проверяем, что у pivot загружено отношение dataEntityable (коллекция данных)
                    if ($pivot->relationLoaded('dataEntityable') && $pivot->dataEntityable) {
                        $dataCollection = $pivot->dataEntityable;
                        
                        // Устанавливаем связь с коллекцией данных через pivot для удобного доступа в шаблонах
                        $entity->setRelation('dataCollection', $dataCollection);
                        
                        // Если у коллекции данных есть link, устанавливаем его как dataCollectionLink
                        if ($dataCollection->relationLoaded('link') && $dataCollection->link) {
                            $entity->setRelation('dataCollectionLink', $dataCollection->link);
                        }
                        
                        // Достаточно одной коллекции (первая, которую нашли)
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Получает ID коллекции и всех её потомков
     */
    private function getCollectionWithDescendantIds(DataCollection $collection): array
    {
        $ids = [$collection->id];
        
        if ($collection->relationLoaded('descendants') && $collection->descendants->isNotEmpty()) {
            $ids = array_merge($ids, $collection->descendants->pluck('id')->toArray());
        }
        
        return $ids;
    }
    
    /**
     * Применяет фильтры из URL-параметров к запросу (без префикса)
     * 
     * @param Builder $query Запрос, к которому применяются фильтры
     * @param Request $request Запрос с параметрами фильтрации
     * @return void
     */
    private function applyUrlFilters(Builder $query, Request $request): void
    {
        // Обработка параметра search напрямую
        if ($request->has('search') && !empty($request->input('search'))) {
            $this->applyFilter($query, 'search', $request->input('search'));
        }
        
        foreach ($request->all() as $param => $value) {
            // Ищем параметры вида filter_{filterName}
            if (strpos($param, "filter_") === 0 && !empty($value)) {
                // Извлекаем имя фильтра
                $filterName = str_replace("filter_", '', $param);
                
                // Применяем фильтр к запросу
                $this->applyFilter($query, $filterName, $value);
            }
        }
    }
    
    /**
     * Применяет фильтры из URL-параметров к запросу (с префиксом)
     * 
     * @param Builder $query Запрос, к которому применяются фильтры
     * @param Request $request Запрос с параметрами фильтрации
     * @param string $keyPrefix Префикс для параметров фильтрации
     * @return void
     */
    private function applyUrlFiltersWithPrefix(Builder $query, Request $request, string $keyPrefix): void
    {
        // Обработка параметра search с префиксом
        if ($request->has("{$keyPrefix}_search") && !empty($request->input("{$keyPrefix}_search"))) {
            $this->applyFilter($query, 'search', $request->input("{$keyPrefix}_search"));
        }
        
        foreach ($request->all() as $param => $value) {
            // Ищем параметры вида {keyPrefix}_filter_{filterName}
            if (strpos($param, "{$keyPrefix}_filter_") === 0 && !empty($value)) {
                // Извлекаем имя фильтра
                $filterName = str_replace("{$keyPrefix}_filter_", '', $param);
                
                // Применяем фильтр к запросу
                $this->applyFilter($query, $filterName, $value);
            }
        }
    }
    
    /**
     * Применяет фильтр к запросу
     * 
     * @param Builder $query Запрос, к которому применяется фильтр
     * @param string $filterName Имя фильтра
     * @param mixed $value Значение фильтра
     * @return void
     */
    private function applyFilter(Builder $query, string $filterName, $value): void
    {
        // Специальная обработка для поиска
        if ($filterName === 'search' && !empty($value)) {
            $searchTerm = '%' . $value . '%';
            
            $query->where(function($q) use ($searchTerm) {
                // Поиск по имени сущности
                $q->where('name', 'like', $searchTerm)
                  // Поиск по атрибутам
                  ->orWhereHas('attributes', function($aq) use ($searchTerm) {
                      $aq->where('value', 'like', $searchTerm);
                  })
                  // Поиск по связанным ссылкам через pivot
                  ->orWhereHas('dataEntityables.link', function($lq) use ($searchTerm) {
                      $lq->where('title', 'like', $searchTerm)
                        ->orWhere('subtitle', 'like', $searchTerm);
                  });
            });
            
            return;
        }
        
        // Проверяем, является ли значение массивом или строкой
        if (is_array($value)) {
            // Для фильтров с множественными значениями
            if (!empty($value)) {
                $query->whereHas('attributes', function ($q) use ($filterName, $value) {
                    $q->where('key', $filterName)
                        ->whereIn('value', $value);
                });
            }
        } else {
            // Для фильтров с одним значением
            $query->whereHas('attributes', function ($q) use ($filterName, $value) {
                $q->where('key', $filterName)
                    ->where('value', $value);
            });
        }
    }
} 