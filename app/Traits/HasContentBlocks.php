<?php

namespace App\Traits;

use App\Models\ContentBlock;
use App\Models\Pivot\ContentBlockable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasContentBlocks
{

  public function contentBlocks(): MorphToMany
  {
    return $this->morphToMany(ContentBlock::class, 'content_blockable')
      ->withPivot('order', 'key')
      ->orderBy('order')->using(ContentBlockable::class);
  }

  public function syncContentBlocks(array $values): void
  {
    $mapped = [];

    foreach ($values as $block) {
      $mapped[$block['id']] = [
        'key' => $block['pivot']['key'] ?? 'default',
        'order' => $block['pivot']['order'] ?? 0
      ];
    }

    $this->contentBlocks()->sync($mapped);
  }

  public function getContentBlocksTree(): Collection
  {
    $this->loadMissing('contentBlocks.descendants');

    return $this->contentBlocks->each(function (ContentBlock $contentBlock) {
      $contentBlock->setRelation('children', $contentBlock->descendants->toTree());
      $contentBlock->unsetRelation('descendants');
    });
  }
}
