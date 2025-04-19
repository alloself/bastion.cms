<?php

namespace App\Traits;

use App\Models\DataCollection;
use App\Models\Pivot\DataCollectionable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;

trait HasDataCollections
{
    /**
     * Полиморфная связь с коллекциями данных.
     */
    public function dataCollections(): MorphToMany
    {
        return $this->morphToMany(DataCollection::class, 'data_collectionable')
            ->withPivot('key', 'order', 'paginate')
            ->orderBy('order')
            ->using(DataCollectionable::class);
    }

    /**
     * Синхронизирует связи с коллекциями данных.
     *
     * Очищаем закешированные отношения, чтобы последующие запросы читали актуальные данные.
     */
    public function syncDataCollections(array $values): void
    {
        $mapped = [];

        foreach ($values as $dataEntity) {
            $mapped[$dataEntity['id']] = [
                'key'      => $dataEntity['pivot']['key'],
                'order'    => $dataEntity['pivot']['order'] ?? 0,
                'paginate' => $dataEntity['pivot']['paginate'] ?? false,
            ];
        }

        // Перезаписываем pivot-записи
        $this->dataCollections()->sync($mapped);

        // Сбрасываем загруженные отношения
        $this->unsetRelation('dataCollections');
        $this->unsetRelation('dataCollections.descendants');
    }

    /**
     * Возвращает дерево коллекций данных с учётом потомков.
     * Всегда перезагружает отношения, чтобы не было кеша.
     */
    public function getDataCollectionsTree(): Collection
    {
        // Используем fresh() для полной перезагрузки модели с указанными отношениями
        $fresh = $this->fresh('dataCollections.descendants');

        return $fresh->dataCollections->map(function (DataCollection $dataCollection) {
            // Устанавливаем детей как дерево
            $tree = $dataCollection->descendants->toTree();
            $dataCollection->setRelation('children', $tree);
            $dataCollection->unsetRelation('descendants');
            return $dataCollection;
        });
    }
}
