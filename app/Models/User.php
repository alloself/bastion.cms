<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasList;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/** @typescript */
class User extends Authenticatable implements AuditableContract
{

    use HasUuids, Notifiable, HasRoles, Auditable, HasList;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /** @typescript */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'phone',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'email_verified_at'
    ];

    protected $auditInclude = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone'  => 'hashed'
        ];
    }

    protected array $searchConfig = [
        'model_fields' => ['first_name', 'last_name', 'middle_name'],
        'full_text' => true
    ];
    protected array $sortable = [
        'first_name',
        'last_name',
        'middle_name',
        'created_at'
    ];
}
