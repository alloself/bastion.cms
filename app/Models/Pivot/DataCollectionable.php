<?php
 
namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
 
class DataCollectionable extends MorphPivot
{
    use HasUuids;
    
    protected $casts = [
        'paginate' => 'boolean'
    ];
}