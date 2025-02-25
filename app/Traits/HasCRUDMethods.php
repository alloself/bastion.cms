<?php

namespace App\Traits;

trait HasCRUDMethods
{
    /**
     * Создать сущность
     */
    public static function createEntity(array $data): self
    {

        $entity = self::create($data);

        if ($entity->isRelation('link') && $data['link']) {
            $entity->addLink($data['link']);
        }


        return $entity;
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
        $this->update($data);


        if ($this->isRelation('link') && $data['link']) {
            $this->updateLink($data['link']);
        }

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
