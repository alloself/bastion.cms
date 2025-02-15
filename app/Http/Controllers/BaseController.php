<?php

namespace App\Http\Controllers;

use App\Traits\HasCRUD;

abstract class BaseController extends Controller
{
    use HasCRUD;
    
    abstract public function model(): string;
    
    protected function allowedRelations(): array
    {
        return [];
    }
    
    protected function validationRules(): array
    {
        return [];
    }
}