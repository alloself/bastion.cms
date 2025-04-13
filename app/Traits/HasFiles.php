<?php

namespace App\Traits;

use App\Models\File;
use App\Models\Pivot\Fileable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFiles
{
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable')
            ->using(Fileable::class)
            ->wherePivot('type', 'file')
            ->withPivot('key', 'order');
    }

    public function syncFiles(array $values): void
    {
        $mapped = collect($values)->mapWithKeys(function ($file) {
            return [
                $file['id'] => [
                    'key' => $file['pivot']['key'] ?? null,
                    'order' => $file['pivot']['order'] ?? 0,
                    'type' => 'file',
                ],
            ];
        });

        $this->files()->sync($mapped->toArray());
    }
}
