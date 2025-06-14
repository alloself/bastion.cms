<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Resources\PageResource;

class PageController extends BaseController
{
    public function model(): string
    {
        return Page::class;
    }
    
    protected function resource(): string
    {
        return PageResource::class;
    }
    
    protected function allowedRelations(): array
    {
        return [
            'template',
            'link',
            'contentBlocks',
            'contentBlocks.children',
            'attributes',
            'images',
            'dataCollections',
            'parent',
            'parent.link',
            'children',
            'children.link',
            'audits',
            'audits.user',
            'files'
        ];
    }
}
