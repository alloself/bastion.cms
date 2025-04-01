<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasCRUDMethods
{
  protected array $syncableRelations = ['data_collections', 'link', 'attributes', 'content_blocks', 'images', 'files'];


  protected function snakeToCamel($input)
  {
    $trimmed = trim($input, '_');

    $camel = preg_replace_callback('/_([a-z])/i', function ($matches) {
      return strtoupper($matches[1]);
    }, $trimmed);

    return lcfirst($camel);
  }


  protected function syncRelations(array $relations): void
  {
    foreach ($relations as $relation => $data) {
      if (!$this->isRelation($this->snakeToCamel($relation)) || !$data) {
        continue;
      }

      $method = 'sync' . ucfirst(Str::camel($relation));

      if (method_exists($this, $method)) {
        $this->$method($data);
      }
    }
  }

  protected function loadChildrenTree(): void
  {
    $this->load('descendants');
    $this->setRelation('children', $this->descendants->toTree($this->id));
    $this->unsetRelation('descendants');
  }

  protected static function prepareRelations(array $relations): array
  {
    $prepared = [];

    foreach ($relations as $relation) {
      if ($relation === 'children') {
        $prepared[] = 'descendants';
      } elseif (strpos($relation, 'children.') === 0) {
        $prepared[] = 'descendants.' . substr($relation, 9);
      } else {
        $prepared[] = $relation;
      }
    }

    return $prepared;
  }

  public static function createEntity(array $data): self
  {
    $syncableRelations = (new static)->syncableRelations;
    $relations = Arr::only($data, $syncableRelations);

    $entity = static::query()->create(Arr::except($data, array_keys($relations)));

    $entity->syncRelations($relations);

    return $entity;
  }

  public static function showEntity($id, array $with = []): self
  {
    $entity = static::with(static::prepareRelations($with))->findOrFail($id);

    if (in_array('children', $with)) {
      $entity->loadChildrenTree();
    }

    if (in_array('contentBlocks', $with)) {
      $entity->getContentBlocksTree();
    }

    return $entity;
  }

  public function updateEntity(array $data, array $with = []): self
  {
    $syncableRelations = (new static)->syncableRelations;
    $relations = Arr::only($data, $syncableRelations);

    $this->update(Arr::except($data, array_keys($relations)));
    $this->syncRelations($relations);

    $this->load($with);

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
