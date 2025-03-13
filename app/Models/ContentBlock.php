<?php

namespace App\Models;

use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class ContentBlock extends BaseModel
{
  use NodeTrait, HasLink;

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
}
