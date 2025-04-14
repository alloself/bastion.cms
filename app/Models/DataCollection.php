<?php

namespace App\Models;

use App\Traits\HasAttributes;
use App\Traits\HasContentBlocks;
use App\Traits\HasDataEntities;
use App\Traits\HasImages;
use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class DataCollection extends BaseModel
{
  use HasFactory, NodeTrait, HasLink, HasContentBlocks, HasAttributes, HasImages, HasDataEntities;

  protected $fillable = ['name', 'meta', 'parent_id', 'page_id', 'order', 'template_id'];

  protected $casts = [
    'meta' => 'object'
  ];

  protected array $searchConfig = [
    'model_fields' => ['name'],
    'relation' => 'link',
    'relation_fields' => ['title', 'url'],
    'full_text_mode' => 'boolean',
    'full_text' => true
  ];

  protected $sortable = ['link.title', 'link.url'];

  public function getParentIdAttribute($value): string | null
  {
    return $value;
  }

  public function page(): BelongsTo
  {
    return $this->belongsTo(Page::class);
  }

  public function template(): BelongsTo
  {
    return $this->belongsTo(Template::class);
  }
}
