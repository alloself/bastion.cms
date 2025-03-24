<?php

namespace App\Traits;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasAttributes
{
    public function attributes(): MorphToMany
    {
        return $this->morphToMany(Attribute::class, 'attributeable')->withPivot('value', 'order');
    }

    /**
     * Синхронизирует атрибуты модели с переданными значениями
     *
     * @param array<int, array{id: int, pivot: array{value: string, order?: int}}> $values
     * @return void
     */
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

        $this->attributes()->sync($syncData);
    }
}
