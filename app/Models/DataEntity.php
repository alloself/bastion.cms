<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DataEntity extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'meta', 'data_collection_id', 'template_id', 'content', 'order'];

    protected $casts = [
        'meta' => 'object'
    ];

    public function links(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DataEntity::class);
    }
}
