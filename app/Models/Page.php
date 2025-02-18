<?php

namespace App\Models;

use App\Traits\HasCRUDMethods;
use App\Traits\HasList;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Page extends Model implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, HasUuids, HasList, Auditable, HasCRUDMethods;
}
