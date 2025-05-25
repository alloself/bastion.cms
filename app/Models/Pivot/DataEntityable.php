<?php

namespace App\Models\Pivot;

use App\Models\DataEntity;
use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Http\Request;

class DataEntityable extends MorphPivot
{
    use HasUuids, HasLink;

    protected $table = 'data_entityables';

    protected $fillable = ['data_entity_id', 'data_entityable_type', 'data_entityable_id', 'key', 'order'];

    public $timestamps = false;

    public static $renderRelations = [
        'dataEntity' => [
            'template',
            'attributes',
            'images',
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
                'dataCollections' => [
                    'attributes',
                    'link',
                    'template',
                    'images',
                ],
            ],
        ],
        'link',
        'dataEntityable'
    ];

    public function dataEntityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function dataEntity(): BelongsTo
    {
        return $this->belongsTo(DataEntity::class);
    }

    public function render(Request $request)
    {
        $this->dataEntity->setRelation('pivot', $this);

        return $this->dataEntity->render($request);
    }
}
