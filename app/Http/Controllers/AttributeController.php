<?php

namespace App\Http\Controllers;

use App\Models\Attribute;

class AttributeController extends BaseController
{
    public function model(): string
    {
        return Attribute::class;
    } 
}
