<?php

namespace App\Models;

use App\Models\Pivot\DataEntityable;
use App\Traits\HasAttributes;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasContentBlocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use App\Models\ContentBlock;

/**
 * @property-read DataCollection|null $dataCollection Соответствующая dataCollection, установленная через transferDataCollectionInfoFromPivots
 * @property-read Link|null $dataCollectionLink Ссылка соответствующей dataCollection, установленная через transferDataCollectionInfoFromPivots
 * @property-read Link|null $link Ссылка из DataEntityable pivot
 */
class DataEntity extends BaseModel
{
    use HasFactory, HasUuids, HasImages, HasAttributes, HasContentBlocks;

    protected $fillable = ['name', 'meta', 'template_id', 'content', 'order', 'parent_id'];

    protected $casts = [
        'meta' => 'object'
    ];

    public static $renderRelations = [
        'template',
        'attributes',
        'images',
        'variants' => [
            'attributes',
            'images',
            'template'
        ],
        'dataEntityables' => [
            'link',
            'dataEntityable' => [
                'link',
                'template',
                'attributes',
                'images'
            ]
        ],
        'contentBlocks' => [
            'attributes',
            'link',
            'template',
            'descendants' => [
                'attributes',
                'link',
                'files',
                'template',
                'images',
            ],
            'files',
            'images',
        ]
    ];

    public function dataEntityables()
    {
        return $this->hasMany(DataEntityable::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DataEntity::class, 'parent_id');
    }
   
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function getLinkAttribute()
    {
       $pivot = $this?->pivot;
       $defaultLink = $this->dataEntityables->first(function ($pivot) {
            return $pivot->link !== null;
        });


        $dataEntityable = $this->dataEntityables->first(function ($item) use ($pivot) {
            return $item->data_entityable_id == $pivot->data_entityable_id && $item->data_entityable_type == $pivot->data_entityable_type;
        });

        return $dataEntityable?->link ?? $defaultLink?->link;
    }

    public static function getRenderRelations(Request $request)
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
