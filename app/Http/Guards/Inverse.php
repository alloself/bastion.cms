<?php

namespace App\Http\Guards;

class Inverse
{
    public static function handle($value)
    {
        return !boolval($value);
    }
}
