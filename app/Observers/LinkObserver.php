<?php


namespace App\Observers;

use App\Models\Link;
use App\Services\LinkUrlGenerator;

class LinkObserver
{
    public function created(Link $link): void
    {
        $this->refreshURL($link);
    }

    public function updated(Link $link): void
    {
        if ($link->isDirty('slug') || $link->isDirty('url')) {
            $this->refreshURL($link);
        }
    }

    protected function refreshURL(Link $link): void
    {
        app(LinkUrlGenerator::class)->generate($link);
    }
}
