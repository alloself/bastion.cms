<?php

namespace App\Models\Pivot;

use App\Models\DataEntity;
use App\Traits\HasLink;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class DataEntityable extends MorphPivot
{
    use HasUuids, HasLink;

    protected $table = 'data_entityables';

    protected $fillable = ['data_entity_id', 'data_entityable_type', 'data_entityable_id', 'key', 'order'];

    public function dataEntityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function dataEntity(): BelongsTo
    {
        return $this->belongsTo(DataEntity::class);
    }
}
