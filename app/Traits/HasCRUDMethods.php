<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HasCRUDMethods
{
    /**
     * Создать сущность
     */
    public static function createEntity(array $data): self
    {
        return self::create($data);
    }

    public static function showEntity($id, array $with = [])
    {
        return self::with($with)->findOrFail($id);
    }

    /**
     * Обновить сущность
     */
    public function updateEntity(array $data): self
    {
        Log::alert($data);
        $this->update($data);
        return $this;
    }

    /**
     * Удалить сущность
     */
    public function deleteEntity(): bool
    {
        return $this->delete();
    }
}
