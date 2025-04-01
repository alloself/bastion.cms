<?php

namespace App\Http\Controllers;

use App\Models\DataCollection;

class DataCollectionController extends BaseController
{
    public function model(): string
    {
        return DataCollection::class;
    } 
}
