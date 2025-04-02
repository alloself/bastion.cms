<?php

namespace App\Models\Pivot;

use App\Models\DataEntity;
use App\Models\Link;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class DataEntityable extends MorphPivot
{
    use HasUuids;

    public function dataEntityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function dataEntity(): BelongsTo
    {
        return $this->belongsTo(DataEntity::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
