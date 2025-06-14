<?php

namespace App\Http\Controllers;

use App\Traits\HasCRUD;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseController extends Controller
{
    use HasCRUD;

    public function __construct()
    {
        JsonResource::withoutWrapping();
    }

    abstract public function model(): string;



    protected function validationRules(): array
    {
        return [];
    }

    protected function resource(): string
    {
        return JsonResource::class;
    }
}
