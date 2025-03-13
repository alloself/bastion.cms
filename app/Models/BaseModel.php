<?php

namespace App\Models;

use App\Traits\HasCRUDMethods;
use App\Traits\HasList;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BaseModel extends Model implements AuditableContract
{
  use HasFactory, HasUuids, HasList, Auditable, HasCRUDMethods, SoftDeletes;
}
