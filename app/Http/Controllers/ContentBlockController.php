<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;
use App\Http\Resources\ContentBlockResource;

class ContentBlockController extends BaseController
{
    public function model(): string
    {
        return ContentBlock::class;
    }
    
    protected function resource(): string
    {
        return ContentBlockResource::class;
    }
    

}
