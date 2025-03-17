<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attribute extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'key'];

    public function attributeable(): MorphTo
    {
        return $this->morphTo();
    }
}
