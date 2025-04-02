<?php

namespace App\Traits;

use App\Models\DataEntity;
use App\Models\Pivot\DataEntityable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasDataEntities
{
    public function dataEntities(): MorphToMany
    {
        return $this->morphToMany(DataEntity::class, 'data_entityable')
            ->withPivot('key', 'order', 'link_id')
            ->using(DataEntityable::class)
            ->orderBy('order');
    }

    public function syncDataEntities(array $values): void
    {
        $mapped = [];

        foreach ($values as $entity) {
            $mapped[$entity['id']] = [
                'key'     => $entity['pivot']['key'] ?? null,
                'order'   => $entity['pivot']['order'] ?? 0,
                'link_id' => $entity['pivot']['link_id'] ?? null,
            ];
        }

        $this->dataEntities()->sync($mapped);
    }
}
