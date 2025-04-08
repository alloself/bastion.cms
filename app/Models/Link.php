<?php

namespace App\Models;

use App\Observers\LinkObserver;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Link extends BaseModel
{
    use HasSlug;

    protected $fillable = ['title', 'subtitle', 'slug', 'url'];

    protected function casts(): array
    {
        return [
            'url' => 'string',
        ];
    }

    protected array $searchConfig = [
        'model_fields' => ['title', 'url'],
        'full_text_mode' => 'boolean',
        'full_text' => true
    ];

    protected static function booted(): void
    {
        static::observe(LinkObserver::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function saveLinkQuietly(): self
    {
        $this->saveQuietly();
        return $this;
    }
}
