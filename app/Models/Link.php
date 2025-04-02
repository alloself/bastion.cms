<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    protected static function booted(): void
    {
        static::updated(function (self $link) {
            if ($link->isDirty('slug') || $link->isDirty('url')) {
                $link->refreshURL();
            }
        });
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

    /**
     * Обновляет URL для текущей модели и её потомков.
     */
    public function refreshURL(): void
    {
        switch ($this->linkable_type) {
            case Page::class:
                $this->generatePageURL();
                break;
            case DataCollection::class:
                $this->generateDataCollectionURL();
                break;
            case DataEntity::class:
                $this->generateDataEntityURL();
                break;
        }

        $this->updateDescendantsURL();
    }

    /**
     * Обновляет URL для всех потомков текущей модели.
     */
    private function updateDescendantsURL(): void
    {
        if (!method_exists($this->linkable, 'children')) {
            return;
        }

        $children = $this->linkable->children()->with('link')->get();

        foreach ($children as $child) {
            if ($child->relationLoaded('link') && $child->link instanceof self) {
                $child->link->refreshURL();
            }
        }
    }

    /**
     * Генерирует URL для страниц (Page).
     */
    public function generatePageURL(): self
    {
        $page = $this->linkable->load('parent.link');

        $this->url = $page->index
            ? '/'
            : $this->buildURLFromAncestors($page->parent, $this->slug);

        return $this->saveLinkQuietly();
    }

    /**
     * Генерирует URL для коллекций данных (DataCollection).
     */
    public function generateDataCollectionURL(): self
    {
        $dataCollection = $this->linkable->load(['ancestors.link', 'page.link']);

        $ancestor = $dataCollection->page ?? $dataCollection->ancestors->last();

        $this->url = $this->buildURLFromAncestors($ancestor, $this->slug);

        return $this->saveLinkQuietly();
    }

    /**
     * Генерирует URL для сущностей данных (DataEntity).
     */
    public function generateDataEntityURL(): self
    {
        /** @var DataEntity $dataEntity */
        $dataEntity = $this->linkable->load('dataEntityables.dataEntityable.link');

        $entityable = $dataEntity->dataEntityables->first();

        if (!$entityable || !$entityable->dataEntityable) {
            $this->url = '/' . trim($this->slug, '/');
            return $this->saveLinkQuietly();
        }

        $parent = $entityable->dataEntityable;

        $this->url = $this->buildURLFromAncestors($parent, $this->slug);

        return $this->saveLinkQuietly();
    }

    /**
     * Строит URL рекурсивно на основе родительских элементов.
     *
     * @param Model|null $startFrom
     * @param string $slug
     * @return string
     */
    protected function buildURLFromAncestors(?Model $startFrom, string $slug): string
    {
        $segments = [];
        $current = $startFrom;

        while ($current) {
            if ($current->relationLoaded('link') && $current->link instanceof self) {
                $segments[] = trim($current->link->slug, '/');
            }
            $current = $current->parent ?? $current->page ?? null;
        }

        $segments = array_reverse($segments);
        $segments[] = $slug;

        $url = implode('/', array_filter($segments));

        return $url === '' ? '/' : "/{$url}";
    }

    /**
     * Тихо сохраняет модель без триггеринга событий.
     */
    private function saveLinkQuietly(): self
    {
        $this->saveQuietly();
        return $this;
    }
}
