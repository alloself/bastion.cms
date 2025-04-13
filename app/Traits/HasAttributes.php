<?php

namespace App\Traits;

use App\Models\Attribute;
use App\Models\Pivot\Attributeable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;

trait HasAttributes
{
    public function attributes(): MorphToMany
    {
        return $this->morphToMany(Attribute::class, 'attributeable')->using(Attributeable::class)->withPivot('value', 'order');
    }

    public function syncAttributes(array $values): void
    {
        if (empty($values)) {
            $this->attributes()->detach();
            return;
        }

        $uniqueAttributes = collect($values)
            ->unique(fn($item) => $item['id'] . '|' . $item['pivot']['value']);

        $syncData = $uniqueAttributes->mapWithKeys(function ($attribute, $index) {
            return [
                $attribute['id'] => [
                    'value' => $attribute['pivot']['value'],
                    'order' => $attribute['pivot']['order'] ?? 0,
                ],
            ];
        })->all();

        Log::alert($syncData);

        $this->attributes()->sync($syncData);
    }
}
