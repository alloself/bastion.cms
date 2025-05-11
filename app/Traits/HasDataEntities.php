<?php

namespace App\Traits;

use App\Models\DataEntity;
use App\Models\Link;
use App\Models\Pivot\DataEntityable;
use App\Services\LinkUrlGenerator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasDataEntities
{
    public function dataEntities(): MorphToMany
    {
        return $this->morphToMany(DataEntity::class, 'data_entityable')
            ->using(DataEntityable::class)
            ->withPivot(['id', 'key', 'order'])
            ->orderBy('order');
    }

    public function syncDataEntities(array $entities): void
    {
        // Получаем текущие pivot записи с их ID
        $currentPivots = $this->dataEntities()
            ->withPivot('id', 'key', 'order')
            ->get()
            ->keyBy('id')
            ->map(function ($item) {
                return [
                    'pivot_id' => $item->pivot->id,
                    'key' => $item->pivot->key,
                    'order' => $item->pivot->order
                ];
            })
            ->toArray();
        
        // Создаем массив для хранения отношений
        $syncData = [];
        
        // Обрабатываем каждую сущность
        foreach ($entities as $item) {
            $pivotData = [
                'key'   => $item['pivot']['key'] ?? null,
                'order' => $item['pivot']['order'] ?? 0,
            ];
            
            $syncData[$item['id']] = $pivotData;
            
            // Обновляем или создаем связанную запись Link
            if (!empty($item['pivot']['link'])) {
                // После синхронизации найдем или создадим pivot модель
                $dataEntityId = $item['id'];
                $existingPivotId = $currentPivots[$dataEntityId]['pivot_id'] ?? null;
                
                // Если pivot запись не существует, мы обработаем её после синхронизации
                if ($existingPivotId) {
                    $dataEntityable = DataEntityable::find($existingPivotId);
                    if ($dataEntityable) {
                        $linkData = $item['pivot']['link'];
                        
                        // Проверяем существует ли уже Link для этой pivot записи
                        $link = $dataEntityable->link;
                        
                        if ($link) {
                            // Обновляем существующую запись Link
                            $link->fill($linkData);
                            app(LinkUrlGenerator::class)->generate($link);
                        } else {
                            // Создаем новую запись Link
                            $link = new Link($linkData);
                            $link->linkable()->associate($dataEntityable);
                            app(LinkUrlGenerator::class)->generate($link);
                        }
                    }
                }
            }
        }
        
        // Синхронизируем отношения
        $this->dataEntities()->sync($syncData);
        
        // Обрабатываем новые pivot записи, которые были только что созданы
        foreach ($entities as $item) {
            if (!empty($item['pivot']['link'])) {
                $dataEntityId = $item['id'];
                
                // Если pivot ID не существовал до синхронизации
                if (!isset($currentPivots[$dataEntityId])) {
                    $pivotModel = $this->dataEntities()
                        ->wherePivot('data_entity_id', $dataEntityId)
                        ->withPivot('id')
                        ->first()
                        ?->pivot;
                        
                    if ($pivotModel) {
                        $dataEntityable = DataEntityable::find($pivotModel->id);
                        if ($dataEntityable && !$dataEntityable->link) {
                            $linkData = $item['pivot']['link'];
                            
                            $link = new Link($linkData);
                            $link->linkable()->associate($dataEntityable);
                            
                            app(LinkUrlGenerator::class)->generate($link);
                        }
                    }
                }
            }
        }
    }

    public function loadDataEntityLinks(): self
    {
        $this->load('dataEntities');

        $pivots = $this->dataEntities
            ->pluck('pivot')
            ->filter(fn($pivot) => $pivot instanceof DataEntityable);

        $morphType = (new DataEntityable)->getMorphClass();

        $links = Link::where('linkable_type', $morphType)
            ->whereIn('linkable_id', $pivots->pluck('id')->all())
            ->get()
            ->keyBy('linkable_id');

        foreach ($pivots as $pivot) {
            $pivot->setRelation('link', $links[$pivot->id] ?? null);
        }

        return $this;
    }
}
