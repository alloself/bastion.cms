<?php

namespace App\Models;

use App\Models\Pivot\DataEntityable;
use App\Traits\HasAttributes;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataEntity extends BaseModel
{
    use HasFactory, HasUuids, HasImages, HasAttributes;

    protected $fillable = ['name', 'meta', 'template_id', 'content', 'order', 'parent_id'];

    protected $casts = [
        'meta' => 'object'
    ];

    public function dataEntityables()
    {
        return $this->hasMany(DataEntityable::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DataEntity::class, 'parent_id');
    }
}
