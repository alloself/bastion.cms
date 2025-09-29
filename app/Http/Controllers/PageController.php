<?php

namespace App\Http\Controllers;

use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends BaseController
{
    public function model(): string
    {
        return Page::class;
    }
    
    protected function allowedRelations(): array
    {
        return [
            'template',
            'link',
            'parent.link',
            'contentBlocks',
            'contentBlocks.children',
            'attributes',
            'images',
            'dataCollections'
        ];
    }

    protected function resource(): string
    {
        return PageResource::class;
    }
}
