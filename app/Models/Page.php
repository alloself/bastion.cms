<?php

namespace App\Models;

use App\Traits\HasCRUDMethods;
use App\Traits\HasLink;
use App\Traits\HasList;
use App\Traits\HasTree;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Kalnoy\Nestedset\NodeTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Page extends Model implements AuditableContract
{
  /** @use HasFactory<\Database\Factories\PageFactory> */
  use HasFactory, HasUuids, HasList, Auditable, HasCRUDMethods, SoftDeletes, NodeTrait, HasLink;

  protected $fillable = ['index', 'meta', 'parent_id', 'template_id'];

  protected $casts = [
    'meta' => 'object',
    'index' => 'boolean',
  ];


  protected array $searchConfig = [
    'model_fields' => ['meta'],
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

  public function transformAudit(array $data): array
  {
    // Проверяем, есть ли изменение в поле template_id
    if (Arr::has($data, 'new_values.template_id')) {
      // Получаем старый и новый шаблоны
      $oldTemplate = Template::find($this->getOriginal('template_id'));
      $newTemplate = Template::find($this->getAttribute('template_id'));

      // Добавляем информацию о шаблонах в данные аудита
      $data['old_values']['template_name'] = $oldTemplate ? $oldTemplate->name : 'Не задано';
      $data['new_values']['template_name'] = $newTemplate ? $newTemplate->name : 'Не задано';
    }

    return $data;
  }

  public function template(): BelongsTo
  {
    return $this->belongsTo(Template::class);
  }

  public function link(): MorphOne
  {
    return $this->morphOne(Link::class, 'linkable');
  }
}
