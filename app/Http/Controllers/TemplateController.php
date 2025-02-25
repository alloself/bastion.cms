<?php

namespace App\Http\Controllers;

use App\Models\Template;

class TemplateController extends BaseController
{
    public function model(): string
    {
        return Template::class;
    }
}
