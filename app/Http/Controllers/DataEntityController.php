<?php

namespace App\Http\Controllers;

use App\Models\DataEntity;
use App\Http\Resources\DataEntityResource;

class DataEntityController extends BaseController
{
    public function model(): string
    {
        return DataEntity::class;
    }

    protected function resource(): string
    {
        return DataEntityResource::class;
    }


}
