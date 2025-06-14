<?php

namespace App\Models;

use App\Traits\HasAttributes;
use App\Traits\HasContentBlocks;
use App\Traits\HasDataEntities;
use App\Traits\HasImages;
use App\Traits\HasLink;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Kalnoy\Nestedset\NodeTrait;
use App\Services\DataEntityService;

class DataCollection extends BaseModel
{
  use HasFactory, NodeTrait, HasLink, HasContentBlocks, HasAttributes, HasImages, HasDataEntities;

  protected $fillable = ['name', 'meta', 'parent_id', 'page_id', 'order', 'template_id'];

  protected $casts = [
    'meta' => 'object'
  ];

  public static $renderRelations = [
    'template',
    'link',
    'attributes',
    'images',
    'dataEntities',
    'contentBlocks' => [
      'attributes',
      'link',
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
          'descendants' => [
            'link'
          ],
        ],
      ],
      'dataCollections' => [
        'descendants' => [
          'link'
        ],
        'images',
        'link',
        'attributes',
        'dataEntities' => [
          'attributes',
        ],
      ],
      'files',
      'images',
      'template',
    ],
    'descendants' => [
      'link',
      'attributes',
      'images',
      'template'
    ],
    'ancestors' => [
      'link',
      'attributes',
      'images',
      'template'
    ]
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

  /**
   * Аксессор для получения корневого элемента с правильно загруженными children
   */
  public function getRootAttribute()
  {
    return $this->getRoot();
  }

  /**
   * Получает корневой элемент коллекции
   */
  public function getRoot()
  {
    if ($this->isRoot()) {
      // Если это корневой элемент, убеждаемся что children загружены с withCount
      if (!$this->relationLoaded('children')) {
        $this->load(['children' => function($query) {
          $query->withCount('dataEntities')->with([
            'link',
            'children' => function($subQuery) {
              $subQuery->withCount('dataEntities')->with('link');
            }
          ]);
        }]);
      }
      return $this;
    }
    
    // Если ancestors загружены, берем первый (корневой)
    if ($this->relationLoaded('ancestors') && $this->ancestors->isNotEmpty()) {
      $root = $this->ancestors->first();
      // Загружаем children для корневого элемента, если они не загружены
      if (!$root->relationLoaded('children')) {
        $root->load(['children' => function($query) {
          $query->withCount('dataEntities')->with([
            'link',
            'children' => function($subQuery) {
              $subQuery->withCount('dataEntities')->with('link');
            }
          ]);
        }]);
      }
      return $root;
    }
    
    // Иначе загружаем корневой элемент через запрос с children и подсчетом dataEntities
    // Загружаем сразу несколько уровней вложенности одним запросом
    return static::whereIsRoot()->with(['children' => function($query) {
      $query->withCount('dataEntities')->with([
        'link',
        'children' => function($subQuery) {
          $subQuery->withCount('dataEntities')->with('link');
        }
      ]);
    }])->first();
  }

  public static function getRenderRelations(Request $request)
  {
    return self::$renderRelations;
  }

  public static function buildDataCollectionsTree(DataCollection $dataCollection)
  {
    if ($dataCollection->relationLoaded('descendants') && $dataCollection->descendants->isNotEmpty()) {
      $children = $dataCollection->descendants->toTree($dataCollection->id);
      
      // Загружаем количество dataEntities для каждого дочернего элемента
      $children->loadCount('dataEntities');
      
      // Загружаем children для каждого дочернего элемента с подсчетом dataEntities
      // Включаем рекурсивную загрузку children.children с withCount одним запросом
      $children->load([
        'children' => function($query) {
          $query->withCount('dataEntities')->with([
            'link',
            'children' => function($subQuery) {
              $subQuery->withCount('dataEntities')->with('link');
            }
          ]);
        }
      ]);

      $dataCollection->setRelation('children', $children);   
    } else {
      $dataCollection->setRelation('children', collect());
    }

    $dataCollection->unsetRelation('descendants');
    
    return $dataCollection;
  }

  public function render(Request $request)
  {
    ContentBlock::buildContentBlocksTree($this->contentBlocks);
    
    $descendants = $this->descendants;
    
    self::buildDataCollectionsTree($this);

    $user = $request->user();

    $header = getItemByPivotKey($this->contentBlocks ?? collect(), 'header');
    $footer = getItemByPivotKey($this->contentBlocks ?? collect(), 'footer');

    $this->setRelation('descendants', $descendants);

    $dataEntityService = new DataEntityService();
    
    $paginationKey = '';
    if (isset($this->pivot) && isset($this->pivot->key) && !empty($this->pivot->key)) {
        $paginationKey = $this->pivot->key;
    }
    
    $dataEntities = $dataEntityService->paginateEntities($this, $request, $paginationKey);
    
    $dataEntityService->transferLinksFromPivots($dataEntities);
    $dataEntityService->transferDataCollectionInfoFromPivots($dataEntities);
    
    $this->setRelation('dataEntities', $dataEntities);
    $this->unsetRelation('descendants');


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
