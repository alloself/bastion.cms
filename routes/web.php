<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/{any?}', [SiteController::class, 'admin'])
    ->where('any', '.*')
    ->name('admin');

Route::get('/{path?}', [SiteController::class, 'render'])
    ->where('path', '^((?!public\/).)*$')
    ->name('web');
