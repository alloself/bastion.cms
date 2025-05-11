<?php

namespace App\Models;

use App\Models\Pivot\DataEntityable;
use App\Traits\HasAttributes;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasContentBlocks;

/**
 * @property-read DataCollection|null $dataCollection Соответствующая dataCollection, установленная через transferDataCollectionInfoFromPivots
 * @property-read Link|null $dataCollectionLink Ссылка соответствующей dataCollection, установленная через transferDataCollectionInfoFromPivots
 */
class DataEntity extends BaseModel
{
    use HasFactory, HasUuids, HasImages, HasAttributes, HasContentBlocks;

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
   
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

}
