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
        $this->dataEntities()->detach();

        foreach ($entities as $item) {
            $pivotData = [
                'key'   => $item['pivot']['key'] ?? null,
                'order' => $item['pivot']['order'] ?? 0,
            ];

            $this->dataEntities()->attach($item['id'], $pivotData);

            $pivotModel = $this->dataEntities()
                ->wherePivot('data_entity_id', $item['id'])
                ->withPivot('id')
                ->first()
                ?->pivot;

            if ($pivotModel && !empty($item['pivot']['link'])) {
                $dataEntityable = DataEntityable::find($pivotModel->id);
                if ($dataEntityable) {
                    $linkData = $item['pivot']['link'];

                    $link = new Link($linkData);
                    $link->linkable()->associate($dataEntityable);

                    app(LinkUrlGenerator::class)->generate($link);
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
