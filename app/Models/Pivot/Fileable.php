<?php

namespace App\Models\Pivot;

use App\Models\File;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class Fileable extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'file_id',
        'fileable_id',
        'fileable_type'
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
