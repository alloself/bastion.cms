<?php

namespace App\Http\Controllers;

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
            'contentBlocks',
            'contentBlocks.children',
            'attributes',
            'images',
            'dataCollections'
        ];
    }
}
