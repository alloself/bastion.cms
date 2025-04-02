<?php

namespace App\Http\Controllers;

use App\Models\DataEntity;

class DataEntityController extends BaseController
{
    public function model(): string
    {
        return DataEntity::class;
    }
}
