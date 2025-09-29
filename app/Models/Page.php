<?php

namespace App\Models;

use App\Traits\HandlesNestedParent;
use App\Traits\HasAttributes;
use App\Traits\HasContentBlocks;
use App\Traits\HasDataCollections;
use App\Traits\HasImages;
use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class Page extends BaseModel
{
  use NodeTrait, HasLink, HasContentBlocks, HasAttributes, HasImages, HasDataCollections, HandlesNestedParent;

  protected $fillable = ['index', 'meta', 'parent_id', 'template_id'];

  protected $casts = [
    'meta' => 'object',
    'index' => 'boolean',
  ];

  public static $renderRelations = [
    'template',
    'contentBlocks' => [
      'attributes',
      'link',
      'dataEntities' => [
        'files',
        'images',
        'attributes',
        'dataEntityables.link'
      ],
      'descendants' => [
        'attributes',
        'link',
        'files',
        'template',
        'images',
        'dataCollections' => [
          'attributes',
          'images',
          'link',
          'descendants',
          'dataEntities' => [
            'files',
            'attributes',
            'images',
            'dataEntityables.link'
          ]
        ],
        'dataEntities' => [
          'files',
          'images',
          'attributes',
          'dataEntityables.link'
        ],
      ],
      'dataCollections' => [
        'descendants',
        'images',
        'link',
        'attributes',
        'dataEntities' => [
          'files',
          'attributes',
          'images',
          'dataEntityables.link'
        ]
      ],
      'files',
      'images',
      'template',
    ]

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

  public static function getRenderRelations()
  {
    return self::$renderRelations;
  }

  public function render(Request $request)
  {
    ContentBlock::buildContentBlocksTree($this->contentBlocks);
    $user = $request->user();

    $header = getItemByPivotKey($this->contentBlocks ?? collect(), 'header');
    $footer = getItemByPivotKey($this->contentBlocks ?? collect(), 'footer');

    $contentBlocks = ContentBlock::renderContentBlocksTree($this->contentBlocks->sortByDesc('pivot.order'), [
      'entity' => $this,
      'user' => $user,
      'header' => $header,
      'footer' => $footer
    ], 0, $request);

    return Blade::render(
      $this->template->value,
      [
        'entity' => $this,
        'header' => $header,
        'contentBlocks' => $contentBlocks,
        'footer' => $footer,
        'user' => $user
      ],
    );
  }
}
