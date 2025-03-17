<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Relations\MorphPivot;
 
class DataCollectionable extends MorphPivot
{
    protected $casts = [
        'paginate' => 'boolean'
    ];
}