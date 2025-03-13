<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HasCRUDMethods
{
  public static function createEntity(array $data): self
  {
    $entity = static::query()->create($data);

    if ($entity->isRelation('link') && isset($data['link'])) {
      $entity->addLink($data['link']);
    }

    if ($entity->isRelation('contentBlocks') && isset($data['content_blocks'])) {
      $entity->syncContentBlocks($data['content_blocks']);
    }

    return $entity;
  }

  public static function getChildrenRelations(array $relations = []): array
  {
    $with = [];

    foreach ($relations as $relation) {
      if ($relation === 'children') {
        $with[] = 'children';
      } elseif (strpos($relation, 'children.') === 0 && method_exists(static::class, 'descendants')) {
        $with[] = 'descendants.' . substr($relation, strlen('children.'));
      }
    }

    return $with;
  }

  public function loadChildrenTree(array $with = []): self
  {
    $this->load('descendants',...$with);

    $childrenTree = $this->descendants->toTree($this->id);

    $this->setRelation('children', $childrenTree);
    $this->unsetRelation('descendants');

    return $this;
  }

  public static function showEntity($id, array $with = []): self
  {
    $childrenRelations = self::getChildrenRelations($with);

    $isChildtenRelationsNeeded = count($childrenRelations);

    if ($isChildtenRelationsNeeded) {
      $with = array_merge($with, $childrenRelations);
    }


    $entity = static::with($with)->findOrFail($id);

    if ($isChildtenRelationsNeeded) {
      $entity->loadChildrenTree($childrenRelations);
    }

    $hasContentBlocks = false;
    foreach ($with as $relation) {
      if (strpos($relation, 'contentBlocks') !== false) {
        $hasContentBlocks = true;
      }
    }
    if ($hasContentBlocks) {
      $entity->getContentBlocksTree();
    }


    return $entity;
  }

  public function updateEntity(array $data, array $with = []): self
  {
    $this->update($data);

    $this->load($with);

    $childrenRelations = self::getChildrenRelations($with);

    $isChildtenRelationsNeeded = count($childrenRelations);

    if ($isChildtenRelationsNeeded) {
      $with = array_merge($with, $childrenRelations);
    }


    if ($isChildtenRelationsNeeded) {
      $this->loadChildrenTree($childrenRelations);
    }

    if ($this->isRelation('link') && isset($data['link'])) {
      $this->updateLink($data['link']);
    }

    if ($this->isRelation('contentBlocks') && isset($data['content_blocks'])) {
      $this->syncContentBlocks($data['content_blocks']);
    }

    $hasContentBlocks = false;
    foreach ($with as $relation) {
      if (strpos($relation, 'contentBlocks') !== false) {
        $hasContentBlocks = true;
      }
    }

    if ($hasContentBlocks) {
      $this->getContentBlocksTree();
    }

    return $this;
  }

  public function deleteEntity(): bool
  {
    if ($this->isRelation('children') && $this->children->isNotEmpty()) {
      $this->children()->delete();
    }

    return $this->delete();
  }
}
