<?php

namespace App\Http\Controllers;

use App\Models\File;

class FileController extends BaseController
{
    public function model(): string
    {
        return File::class;
    }
}
