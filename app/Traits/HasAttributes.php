<?php

namespace App\Traits;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasAttributes
{
    public function attributes(): MorphToMany
    {
        return $this->morphToMany(Attribute::class, 'attributeable')->withPivot('value');
    }

    /**
     * Синхронизирует атрибуты модели с переданными значениями
     *
     * @param array<int, array{id: int, pivot: array{value: string}}> $values
     * @return void
     */
    public function syncAttributes(array $values): void
    {
        if (empty($values)) {
            $this->attributes()->detach();
            return;
        }

        $uniqueAttributes = collect($values)->unique(fn(array $item): string => 
            $item['id'] . $item['pivot']['value']
        );

        $syncData = $uniqueAttributes->mapWithKeys(fn(array $attribute): array => 
            [$attribute['id'] => ['value' => $attribute['pivot']['value']]]
        )->all();

        $this->attributes()->sync($syncData);
    }
}
