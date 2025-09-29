<?php

namespace App\Models;

use App\Traits\HandlesNestedParent;
use App\Traits\HasAttributes;
use App\Traits\HasDataCollections;
use App\Traits\HasDataEntities;
use App\Traits\HasImages;
use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Collection;

class ContentBlock extends BaseModel
{
  use NodeTrait, HasLink, HasImages, HasDataEntities, HasDataCollections, HasAttributes, HandlesNestedParent;

  protected $fillable = ['name', 'content', 'order', 'parent_id', 'template_id'];

  protected array $searchConfig = [
    'model_fields' => ['name'],
    'full_text_mode' => 'boolean',
    'full_text' => true
  ];

  public function getParentIdAttribute($value): string | null
  {
    return $value;
  }

  public function template(): BelongsTo
  {
    return $this->belongsTo(Template::class);
  }
  public static function renderContentBlocksTree(Collection $contentBlocks, array $globalVariables = [], int $depth = 0, Request $request): array
  {
    $tree = [];
    $sortKey = $depth == 0 ? 'pivot.order' : 'order';

    foreach ($contentBlocks->sortByDesc($sortKey) as $key => $block) {
      if (count($block->children)) {
        $block->renderedChildren = ContentBlock::renderContentBlocksTree(
          $block->children,
          $globalVariables,
          $depth + 1,
          $request
        );
      }
      $template = $block->template->value;
      $template = preg_replace('/^<\w+/im', "$0 id='block-$block->id'", trim($template));

      $tree[] = Blade::render(
      $template,
        [
          'contentBlock' => $block,
          'loop' => ['index' => $key, 'total' => $contentBlocks->count()],
          ...$globalVariables
        ]
      );
    }

    return $tree;
  }

  public static function buildContentBlocksTree(Collection $contentBlocks)
  {
    $contentBlocks->each(function ($contentBlock) {
      if ($contentBlock->relationLoaded('descendants') && $contentBlock->descendants->isNotEmpty()) {
        $contentBlock->setRelation('children', $contentBlock->descendants->toTree($contentBlock->id));
        $contentBlock->unsetRelation('descendants');
      } else {
        $contentBlock->setRelation('children', collect());
      }
    });
  }

  /**
   * Формирует и загружает отношение children на основе загруженных descendants.
   */
  public function getChildrenTree(): self
  {
    $this->loadMissing('descendants');

    if ($this->relationLoaded('descendants') && $this->descendants->isNotEmpty()) {
      $this->setRelation('children', $this->descendants->toTree($this->id));
      $this->unsetRelation('descendants');
    } else {
      $this->setRelation('children', collect());
    }

    return $this;
  }
}
