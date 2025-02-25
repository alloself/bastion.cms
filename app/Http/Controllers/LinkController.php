<?php

namespace App\Http\Controllers;

use App\Models\Link;

class LinkController extends BaseController
{
    public function model(): string
    {
        return Link::class;
    } 
}
