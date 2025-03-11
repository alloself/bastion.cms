<?php

namespace App\Models;

use App\Traits\HasCRUDMethods;
use App\Traits\HasList;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ContentBlock extends Model implements AuditableContract
{
  use HasFactory, HasUuids, HasList, Auditable, HasCRUDMethods, SoftDeletes, NodeTrait;

  protected $fillable = ['name', 'content', 'order', 'parent_id', 'template_id'];

  protected array $searchConfig = [
    'model_fields' => ['name'],
    'full_text_mode' => 'boolean',
    'full_text' => true
  ];


  public function template(): BelongsTo
  {
    return $this->belongsTo(Template::class);
  }
}
