<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFiles
{
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable')
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
