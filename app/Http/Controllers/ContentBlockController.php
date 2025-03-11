<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;

class ContentBlockController extends BaseController
{
    public function model(): string
    {
        return ContentBlock::class;
    } 
}
