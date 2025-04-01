<?php
 
namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
 
class ContentBlockable extends MorphPivot
{
    use HasUuids;
    
}