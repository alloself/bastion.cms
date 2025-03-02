<?php

namespace App\Traits;

trait HasCRUDMethods
{
  public function createEntity(array $data): self
  {

    $entity = self::create($data);

    if ($entity->isRelation('link') && $data['link']) {
      $entity->addLink($data['link']);
    }


    return $entity;
  }


  public static function getChildrenRelations($relations = [])
  {
    $with = [];

    foreach ($relations as $relation) {
      if ($relation === 'children') {
        $with[] = 'children';
      } elseif (strpos($relation, 'children.') === 0) {
        $with[] = 'descendants.' . substr($relation, strlen('children.'));
      }
    }

    return $with;
  }

  public function loadChildrenTree(): self
  {
    if (!$this->relationLoaded('descendants')) {
      $this->load('descendants');
    }
    $childrenTree = $this->descendants->toTree($this->id);
    $this->setRelation('children', $childrenTree);


    $this->unsetRelation('descendants');

    return $this;
  }

  public static function showEntity($id, array $with = [])
  {

    $childrenRelations = self::getChildrenRelations($with);

    $isChildtenRelationsNeeded = count($childrenRelations);

    if ($isChildtenRelationsNeeded) {
      $with = array_merge($with, $childrenRelations);
    }

    $entity = self::with($with)->findOrFail($id);

    if ($isChildtenRelationsNeeded) {
      $entity->loadChildrenTree();
    }

    return $entity;
  }

  public function updateEntity(array $data): self
  {
    $this->update($data);


    if ($this->isRelation('link') && $data['link']) {
      $this->updateLink($data['link']);
    }

    return $this;
  }

  public function deleteEntity(): bool
  {
    return $this->delete();
  }
}
