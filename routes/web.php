<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/{any?}', [SiteController::class, 'admin'])
    ->where('any', '.*')
    ->name('admin');

Route::get('/{path?}', [SiteController::class, 'site'])
    ->where('path', '^((?!public\/).)*$')
    ->name('site');
