<?php

namespace App\Http\Guards;

use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public static function handle()
    {
        return boolval(Auth::user());
    }
}
