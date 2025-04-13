<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;

class DataCollectionController extends BaseController
{
    public function model(): string
    {
        return DataCollection::class;
    }

    protected function resource(): string
    {
        return DataCollectionResource::class;
    }
}
