<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Page;
use App\Models\DataCollection;
use App\Models\Pivot\DataEntityable;

class LinkUrlGenerator
{
    public function generate(Link $link): void
    {
        $linkable = $link->linkable;

        if (!$linkable) {
            return;
        }

        switch ($link->linkable_type) {
            case Page::class:
                $this->generatePageURL($link, $linkable);
                break;
            case DataCollection::class:
                $this->generateDataCollectionURL($link, $linkable);
                break;
            case DataEntityable::class:
                $this->generateDataEntityableURL($link, $linkable);
                break;
        }

        $this->updateDescendantsURL($link, $linkable);
    }

    protected function generatePageURL(Link $link, Page $page): void
    {
        $page->loadMissing('parent.link');

        $link->url = $page->index
            ? '/'
            : $this->buildURLFromParent($page->parent, $link->slug);

        $link->saveLinkQuietly();
    }

    protected function generateDataCollectionURL(Link $link, DataCollection $collection): void
    {
        $collection->loadMissing(['page.link', 'parent.link']);
        $ancestor = $collection->page ?? $collection->parent;

        $link->url = $this->buildURLFromParent($ancestor, $link->slug);
        $link->saveLinkQuietly();
    }

    protected function generateDataEntityableURL(Link $link, DataEntityable $entityable): void
    {
        $entityable->loadMissing(['dataEntity', 'dataEntityable.link']);

        $parent = $entityable->dataEntityable;
        $slug = $entityable->dataEntity->slug ?? $link->slug;

        $link->url = $this->buildURLFromParent($parent, $slug);
        $link->saveLinkQuietly();
    }

    protected function buildURLFromParent(?object $parent, string $slug): string
    {
        if (
            $parent &&
            $parent->relationLoaded('link') &&
            $parent->link &&
            filled($parent->link->url)
        ) {
            $base = rtrim($parent->link->url, '/');
            return $base === '' ? "/{$slug}" : "{$base}/{$slug}";
        }

        return '/' . trim($slug, '/');
    }

    protected function updateDescendantsURL(Link $link, object $model): void
    {
        // 1. children()
        if (method_exists($model, 'children')) {
            $children = $model->children()->with('link')->get();

            foreach ($children as $child) {
                if ($child->link) {
                    $this->generate($child->link);
                }
            }
        }

        // 2. dataCollections()
        if (method_exists($model, 'dataCollections')) {
            $collections = $model->dataCollections()->with('link')->get();

            foreach ($collections as $collection) {
                if ($collection->link) {
                    $this->generate($collection->link);
                }

                $entities = $collection->dataEntities()->get();

                foreach ($entities as $entity) {
                    // Обновляем все link'и через dataEntityables
                    $entity->loadMissing('dataEntityables.link');

                    foreach ($entity->dataEntityables as $relation) {
                        if ($relation->link) {
                            $this->generate($relation->link);
                        }
                    }

                    // Обновляем variants рекурсивно
                    $variants = $entity->variants()->with('link')->get();

                    foreach ($variants as $variant) {
                        if ($variant->link) {
                            $this->generate($variant->link);
                        }
                    }
                }
            }
        }
    }
}
