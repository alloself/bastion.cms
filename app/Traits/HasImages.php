<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasImages
{
    use HasFiles;

    public function images(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable')
            ->wherePivot('type', 'image')
            ->withPivot('key', 'order');
    }

    public function syncImages(array $values): void
    {
        $mapped = collect($values)->mapWithKeys(function ($image) {
            return [
                $image['id'] => [
                    'key' => $image['pivot']['key'] ?? null,
                    'order' => $image['pivot']['order'] ?? 0,
                    'type' => 'image',
                ],
            ];
        });

        $this->images()->sync($mapped->toArray());
    }
}
