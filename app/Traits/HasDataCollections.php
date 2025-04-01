<?php

namespace App\Traits;

use App\Models\DataCollection;
use App\Models\Pivot\DataCollectionable as PivotDataCollectionable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;

trait HasDataCollections
{
    public function dataCollections(): MorphToMany
    {
        return $this->morphToMany(DataCollection::class, 'data_collectionable')
            ->withPivot('key', 'order', 'paginate')->orderBy('order')
            ->using(PivotDataCollectionable::class);
    }

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

        $this->dataCollections()->sync($mapped);
    }
}
