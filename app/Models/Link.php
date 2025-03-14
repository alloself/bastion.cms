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

  protected static function booted()
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

  public function refreshURL(): void
  {
    match ($this->linkable_type) {
      Page::class => $this->generatePageURL(),
      //DataCollection::class => $this->generateDataCollectionURL(),
      //DataEntity::class => $this->generateDataEntityURL(),
      default => null,
    };

    $this->updateDescendantsURL();
  }

  private function updateDescendantsURL(): void
  {
    $this->linkable->children?->each(function ($child) {
      $child->link->refreshURL();
    });
  }

  public function generatePageURL(): self
  {
    $page = $this->linkable->load('parent.link');

    $this->url = match (true) {
      $page->index => '/',
      default => $this->buildURLFromAncestors($page->parent, $this->slug)
    };

    $this->saveQuietly();

    return $this;
  }

  public function generateDataCollectionURL(): self
  {
    $dataCollection = $this->linkable->load([
      'ancestors.link',
      'page.link'
    ]);

    $this->url = $this->buildURLFromAncestors(
      $dataCollection->page ?? $dataCollection->ancestors->last(),
      $this->slug
    );

    return $this->saveWithEvents();
  }

  public function generateDataEntityURL(): self
  {
    $dataEntity = $this->linkable->load([
      'dataCollection.link.page.link'
    ]);

    $this->url = $this->buildURLFromAncestors(
      $dataEntity->dataCollection,
      $this->slug
    );

    return $this->saveWithEvents();
  }

  protected function buildURLFromAncestors(?Model $startFrom, string $slug): string
  {
    $segments = [];
    $current = $startFrom;

    while ($current) {
      if ($current->link) {

        $segments[] = trim($current->link->slug, '/');
      }
      $current = $current->parent ?? $current->page ?? null;
    }

    $segments = array_reverse($segments);
    $segments[] = $slug;

    $url = implode('/', array_filter($segments));

    return $url === '' ? '/' : "/{$url}";
  }

  private function saveWithEvents(): self
  {
    $this->save();
    return $this;
  }
}
