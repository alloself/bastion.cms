<?php

namespace App\Traits;

use Kalnoy\Nestedset\NodeTrait;

trait HasTree
{
  use NodeTrait;

  public static function showEntity($id, array $with = [])
  {
    $customWith = [];
    $needsChildrenTree = false;

    foreach ($with as $relation) {
      if ($relation === 'children') {
        $needsChildrenTree = true;
      } elseif (strpos($relation, 'children.') === 0) {
        $customWith[] = 'descendants.' . substr($relation, strlen('children.'));
        $needsChildrenTree = true;
      } else {
        $customWith[] = $relation;
      }
    }

    $entity = self::with($customWith)->findOrFail($id);

    if ($needsChildrenTree) {
      if (!$entity->relationLoaded('descendants')) {
        $entity->load('descendants');
      }
      $childrenTree = $entity->descendants->toTree($entity->id);
      $entity->setRelation('children', $childrenTree);
    }

    $entity->unsetRelation('descendants');

    return $entity;
  }
}
