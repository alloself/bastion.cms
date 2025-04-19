<?php

namespace App\Models;

use App\Traits\HasAttributes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends BaseModel
{
    use HasAttributes;

    protected $fillable = ['name', 'value'];

    protected array $searchConfig = [
        'model_fields' => ['name', 'value'],
        'full_text_mode' => 'boolean',
        'full_text' => true
    ];

    protected $sortable = ['name'];

    /**
     * Get content blocks that use this template
     */
    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class);
    }

    /**
     * Get pages that use this template
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
