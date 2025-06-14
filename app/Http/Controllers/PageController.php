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
    

}
